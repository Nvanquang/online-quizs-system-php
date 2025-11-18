const $ = window.$

$(document).ready(() => {
  // Quiz Configuration
  const QUIZ_CONFIG = {
    timeLimit: 10, // default time limit
    maxScore: 1000,
    typingSpeedMs: 50,
    imageLoadTimeout: 1200,
    answerRevealDelay: 120,
    timerStartDelay: 220,
    explanationDuration: 5000,
    noExplanationDelay: 1000,
    imageFadeInDuration: 300,
    explanationFadeInDuration: 200,
    answerFadeInDuration: 180,
  }

  const questions = Array.isArray(window.QUIZ_QUESTIONS) ? window.QUIZ_QUESTIONS : []

  const gameState = {
    currentQuestion: 0,
    score: QUIZ_CONFIG.maxScore,
    totalScore: 0, // Tổng điểm tích lũy qua các câu hỏi
    timeRemaining: QUIZ_CONFIG.timeLimit,
    answered: false,
    selectedAnswer: null,
    correctAnswer: null,
    timerInterval: null,
    scoreInterval: null,
    showCorrectAnswerCalled: false,
    typingInterval: null,
    isPaused: false, // Trạng thái pause
    pausedTimeRemaining: 0, // Lưu thời gian còn lại khi pause
    pausedScore: 0, // Lưu điểm khi pause
    answerHistory: [], // Lưu lịch sử trả lời của từng câu
  }

  // ===== Initialization =====

  /**
   * Khởi tạo quiz game
   * Bắt đầu load câu hỏi đầu tiên
   */
  function init() {
    setupPauseButton()
    setupNextButton()
    setupFullscreenButton()
    setupReportButton()
    setupLeaderboardButton()
    setupDoneButton()
    loadQuestion(gameState.currentQuestion)
  }

  // ===== Pause/Resume Functionality =====

  /**
   * Setup event handler cho nút pause/resume
   */
  function setupPauseButton() {
    $('#pause-btn').on('click', function () {
      if (gameState.isPaused) {
        resumeGame()
      } else {
        pauseGame()
      }
    })
  }

  /**
   * Setup event handler cho nút next (skip câu hỏi)
   */
  function setupNextButton() {
    $('#next-btn').on('click', function () {
      skipCurrentQuestion()
    })
  }

  /**
   * Setup event handler cho nút fullscreen
   */
  function setupFullscreenButton() {
    $('#fullscreen-btn').on('click', function () {
      toggleFullscreen()
    })
  }

  /**
   * Setup event handler cho nút view report
   */
  function setupReportButton() {
    $('#view-report-btn').on('click', function () {
      showReportScreen()
    })
  }

  /**
   * Setup event handler cho nút view leaderboard
   */
  function setupLeaderboardButton() {
    $('#view-leaderboard-btn').on('click', function () {
      backToResultsScreen()
    })
  }

  /**
   * Setup event handler cho nút Done: gửi dữ liệu kết thúc game
   */
  function setupDoneButton() {
    $('#done-btn').on('click', async function () {
      const $btn = $(this)
      if ($btn.prop('disabled')) return

      try {
        $btn.prop('disabled', true).addClass('disabled').text('Submitting...')
        const payload = buildEndGamePayload()
        await postEndGame(payload)
        // Redirect sau khi submit thành công (điều chỉnh route nếu có trang summary)
        window.location.href = '/'
      } catch (err) {
        console.error('Submit end game failed:', err)
        alert('Failed to submit results. Please try again.')
        $btn.prop('disabled', false).removeClass('disabled').html('<i class="fas fa-check"></i> Done')
      }
    })
  }

  /**
   * Tạo payload kết thúc game để gửi lên server
   */
  function buildEndGamePayload() {
    const total_questions = questions.length
    const correct_answers = gameState.answerHistory.filter(h => h.isCorrect).length
    // const accuracy = totalQuestions > 0 ? Math.round((correctAnswers / totalQuestions) * 100) : 0

    // Chỉ gửi tổng quan, không gửi chi tiết từng câu
    return {
      session_code: (window.SESSION_CODE || '').toString(),
      id: Number(window.SESSION_PLAYER_ID) || null,
      total_score: Number(gameState.totalScore),
      total_questions: Number(total_questions),
      correct_answers: Number(correct_answers),
      // accuracy: accuracy
    }
  }

  /**
 * Gửi POST tới /game/end/{sessionCode} - Sử dụng form data để $_POST
 */
  async function postEndGame(payload) {
    const session_code = payload.session_code;
    if (!session_code) throw new Error('Missing session code');

    const url = `/game/end/${encodeURIComponent(session_code)}`;

    const formData = new URLSearchParams();
    for (const [key, value] of Object.entries(payload)) {
      formData.append(key, value);
    }

    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-Token': (window.CSRF_TOKEN || '').toString()
      },
      body: formData,
      credentials: 'same-origin'
    });

    if (!res.ok) {
      const text = await res.text().catch(() => '');
      throw new Error(`HTTP ${res.status}: ${text}`);
    }

    return res.json().catch(() => ({}));
  }

  /**
   * Toggle chế độ fullscreen
   */
  function toggleFullscreen() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(err => {
        console.log('Error attempting to enable fullscreen:', err)
      })
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen()
      }
    }
  }

  // Listen for fullscreen changes
  document.addEventListener('fullscreenchange', function () {
    const $btn = $('#fullscreen-btn')
    if (document.fullscreenElement) {
      $btn.find('i').removeClass('fa-expand').addClass('fa-compress')
    } else {
      $btn.find('i').removeClass('fa-compress').addClass('fa-expand')
    }
  })

  /**
   * Tạm dừng game
   */
  function pauseGame() {
    if (gameState.answered || gameState.isPaused) return

    gameState.isPaused = true
    gameState.pausedTimeRemaining = gameState.timeRemaining
    gameState.pausedScore = gameState.score

    // Dừng tất cả intervals
    if (gameState.timerInterval) clearInterval(gameState.timerInterval)
    if (gameState.scoreInterval) clearInterval(gameState.scoreInterval)
    if (gameState.typingInterval) clearInterval(gameState.typingInterval)

    // Thay đổi icon thành play (tam giác)
    const $pauseBtn = $('#pause-btn')
    $pauseBtn.attr('title', 'Resume')
    $pauseBtn.find('i').removeClass('fa-pause').addClass('fa-play')

    // Disable các nút đáp án khi pause
    $('.answer-btn').addClass('disabled')
  }

  /**
   * Tiếp tục game sau khi pause
   * Fast-forward UI display để đảm bảo câu hỏi, hình ảnh, đáp án hiện đầy đủ
   */
  function resumeGame() {
    if (!gameState.isPaused) return

    const question = questions[gameState.currentQuestion]
    if (!question) return // Safety check

    gameState.isPaused = false
    gameState.timeRemaining = gameState.pausedTimeRemaining
    gameState.score = gameState.pausedScore

    // Fast-forward: Hiển thị full text câu hỏi ngay lập tức (bỏ typing effect)
    $('#question-text').text(question.text || '')

    // Xử lý hình ảnh (load async nếu cần, fade-in khi ready, nhưng không block timer)
    const imageSrc = question.image || ''
    const $img = $('#question-image')
    if (imageSrc) {
      setupImageLoadHandlers($img, imageSrc, () => { }) // Empty callback, không chờ
    } else {
      $img.hide()
    }

    // Hiển thị tất cả đáp án ngay lập tức (không stagger delay)
    const $answerBtns = $('.answer-btn')
    fillAnswerButtons($answerBtns, question)
    $answerBtns.show() // Show all at once

    // Thay đổi icon thành pause
    const $pauseBtn = $('#pause-btn')
    $pauseBtn.attr('title', 'Pause')
    $pauseBtn.find('i').removeClass('fa-play').addClass('fa-pause')

    // Enable lại các nút đáp án (chỉ nếu chưa answered)
    if (!gameState.answered) {
      $('.answer-btn').removeClass('disabled')
    }

    // Tiếp tục các timers ngay lập tức
    startQuestionTimer()
    updateScoreUI() // Update score bar ngay
  }

  /**
   * Bỏ qua câu hỏi hiện tại và chuyển sang câu tiếp theo
   */
  function skipCurrentQuestion() {
    // Nếu đang pause thì resume trước
    if (gameState.isPaused) {
      resumeGame()
    }

    // Đánh dấu đã trả lời để tránh conflict
    gameState.answered = true
    gameState.showCorrectAnswerCalled = true

    // Lưu lịch sử: skip = không trả lời (mặc định sai)
    const question = questions[gameState.currentQuestion]
    const duration = getQuestionTimeLimit(question)
    const timeSpent = Math.max(0, duration - gameState.timeRemaining)

    gameState.answerHistory.push({
      questionIndex: gameState.currentQuestion,
      selectedAnswer: null,
      correctAnswer: gameState.correctAnswer,
      isCorrect: false,
      timeSpent: timeSpent,
      earnedPoints: 0
    })

    // Dừng tất cả intervals
    stopAllIntervals()

    // Chuyển sang câu tiếp theo ngay lập tức
    gameState.currentQuestion++
    loadQuestion(gameState.currentQuestion)
  }

  // ===== Question Loading =====

  /**
   * Load và hiển thị câu hỏi theo index
   * @param {number} index - Index của câu hỏi cần load
   */
  function loadQuestion(index) {
    if (index >= questions.length) {
      endQuiz()
      return
    }

    const question = questions[index]

    updateSlideInfo(index)
    resetGameState(question)
    clearIntervals()
    resetUI()
    startTypingEffect(question)
    updateScoreUI()
  }

  /**
   * Cập nhật thông tin số slide hiện tại
   * @param {number} index - Index câu hỏi hiện tại
   */
  function updateSlideInfo(index) {
    $('#slide-info').text(`Slide ${index + 1}/${questions.length}`)
  }

  /**
   * Reset trạng thái game cho câu hỏi mới
   * @param {Object} question - Object câu hỏi
   */
  function resetGameState(question) {
    gameState.answered = false
    gameState.selectedAnswer = null
    gameState.correctAnswer = normalizeCorrectAnswer(question.correctAnswer)
    gameState.score = QUIZ_CONFIG.maxScore
    gameState.timeRemaining = getQuestionTimeLimit(question)
    gameState.showCorrectAnswerCalled = false
    gameState.isPaused = false
    gameState.pausedTimeRemaining = 0
    gameState.pausedScore = 0
  }

  /**
   * Lấy thời gian giới hạn cho câu hỏi
   * @param {Object} question - Object câu hỏi
   * @returns {number} Thời gian giới hạn (giây)
   */
  function getQuestionTimeLimit(question) {
    return Number.isFinite(question?.timeLimit) && question.timeLimit > 0
      ? Number(question.timeLimit)
      : QUIZ_CONFIG.timeLimit
  }

  /**
   * Clear tất cả intervals đang chạy
   */
  function clearIntervals() {
    if (gameState.timerInterval) clearInterval(gameState.timerInterval)
    if (gameState.scoreInterval) clearInterval(gameState.scoreInterval)
    if (gameState.typingInterval) clearInterval(gameState.typingInterval)
  }

  /**
   * Reset giao diện về trạng thái ban đầu
   */
  function resetUI() {
    $('.answer-btn')
      .removeClass('disabled answered show-check dimmed')
      .css('opacity', '')
      .hide()

    $('.answer-check').hide()

    const $img = $('#question-image')
    $img.hide().css('filter', '')

    const $expl = $('#explanation-box')
    $expl.stop(true, true).hide().text('')

    const $qt = $('#question-text')
    $qt.stop(true, true).text('')
  }

  // ===== Typing Effect =====

  /**
   * Bắt đầu hiệu ứng typing cho text câu hỏi
   * @param {Object} question - Object câu hỏi
   */
  function startTypingEffect(question) {
    const $qt = $('#question-text')
    const text = question.text || ''
    const speed = getTypingSpeed()
    let charIndex = 0

    gameState.typingInterval = setInterval(() => {
      if (charIndex >= text.length) {
        clearInterval(gameState.typingInterval)
        gameState.typingInterval = null
        handleImageLoading(question)
        return
      }
      $qt.text($qt.text() + text.charAt(charIndex))
      charIndex++
    }, speed)
  }

  /**
   * Lấy tốc độ typing từ config hoặc window
   * @returns {number} Tốc độ typing (ms)
   */
  function getTypingSpeed() {
    if (typeof window !== 'undefined' && typeof window.TYPE_SPEED_MS === 'number') {
      return window.TYPE_SPEED_MS
    }
    return typeof QUIZ_CONFIG.typingSpeedMs === 'number' ? QUIZ_CONFIG.typingSpeedMs : 35
  }

  // ===== Image Loading =====

  /**
   * Xử lý load và hiển thị ảnh câu hỏi
   * @param {Object} question - Object câu hỏi
   */
  function handleImageLoading(question) {
    const $img = $('#question-image')
    const imageSrc = question.image || ''

    $img.off('load._qimg error._qimg')

    if (!imageSrc) {
      $img.attr('src', '').hide()
      revealAnswersAndStartTimer(question)
      return
    }

    setupImageLoadHandlers($img, imageSrc, () => {
      revealAnswersAndStartTimer(question)
    })
  }

  /**
   * Setup các handlers cho việc load ảnh (load, error, timeout)
   * @param {jQuery} $img - jQuery object của image element
   * @param {string} imageSrc - URL của ảnh
   * @param {Function} callback - Callback khi ảnh load xong hoặc timeout
   */
  function setupImageLoadHandlers($img, imageSrc, callback) {
    let imgSettled = false

    const settle = () => {
      if (imgSettled) return
      imgSettled = true
      $img.off('load._qimg error._qimg')
      callback()
    }

    const imgTimeout = setTimeout(settle, QUIZ_CONFIG.imageLoadTimeout)

    $img.on('load._qimg', function () {
      clearTimeout(imgTimeout)
      $(this).fadeIn(QUIZ_CONFIG.imageFadeInDuration, settle)
    }).on('error._qimg', function () {
      clearTimeout(imgTimeout)
      $(this).hide()
      settle()
    })

    $img.attr('src', imageSrc)

    if ($img[0].complete) {
      clearTimeout(imgTimeout)
      $img.fadeIn(QUIZ_CONFIG.imageFadeInDuration, settle)
    }
  }

  // ===== Answer Reveal =====

  /**
   * Hiển thị các đáp án và bắt đầu timer
   * @param {Object} question - Object câu hỏi
   */
  function revealAnswersAndStartTimer(question) {
    const $answerBtns = $('.answer-btn')

    fillAnswerButtons($answerBtns, question)
    staggerAnswerReveal($answerBtns)
    scheduleTimerStart($answerBtns)
  }

  /**
   * Điền text vào các button đáp án
   * @param {jQuery} $answerBtns - jQuery object của các answer buttons
   * @param {Object} question - Object câu hỏi
   */
  function fillAnswerButtons($answerBtns, question) {
    $answerBtns.each(function (i) {
      const answerText = Array.isArray(question.answers) ? (question.answers[i] || '') : ''
      $(this).find('.answer-text').text(answerText)
      $(this).attr('data-answer', i)
    })
  }

  /**
   * Hiển thị các đáp án lần lượt với hiệu ứng stagger
   * @param {jQuery} $answerBtns - jQuery object của các answer buttons
   */
  function staggerAnswerReveal($answerBtns) {
    $answerBtns.each(function (idx) {
      const btn = this
      setTimeout(() => {
        $(btn).fadeIn(QUIZ_CONFIG.answerFadeInDuration)
      }, idx * QUIZ_CONFIG.answerRevealDelay)
    })
  }

  /**
   * Lên lịch để bắt đầu timer sau khi đáp án cuối được hiển thị
   * @param {jQuery} $answerBtns - jQuery object của các answer buttons
   */
  function scheduleTimerStart($answerBtns) {
    const delay = $answerBtns.length
      ? ($answerBtns.length - 1) * QUIZ_CONFIG.answerRevealDelay + QUIZ_CONFIG.timerStartDelay
      : 0

    setTimeout(() => {
      startQuestionTimer()
    }, delay)
  }

  // ===== Timers =====

  /**
   * Bắt đầu cả countdown timer và score decrement
   */
  function startQuestionTimer() {
    if (gameState.isPaused) return // Không start timer nếu đang pause

    startCountdownTimer()
    startScoreDecrement()
  }

  /**
   * Bắt đầu đếm ngược thời gian
   * Khi hết giờ sẽ gọi handleTimeOut()
   */
  function startCountdownTimer() {
    gameState.timerInterval = setInterval(() => {
      if (gameState.isPaused) return // Skip khi pause

      gameState.timeRemaining--

      // Cập nhật hiển thị thời gian trên UI (nếu có element)
      updateTimerDisplay()

      if (gameState.timeRemaining <= 0) {
        clearInterval(gameState.timerInterval)
        if (!gameState.answered) {
          handleTimeOut()
        }
      }
    }, 1000)
  }

  /**
   * Cập nhật hiển thị thời gian còn lại (nếu cần)
   */
  function updateTimerDisplay() {
    // Có thể thêm hiển thị countdown trên UI nếu muốn
    // Ví dụ: $('#timer-display').text(gameState.timeRemaining)
  }

  /**
   * Bắt đầu giảm điểm theo thời gian
   * Điểm giảm dần khi chưa trả lời
   */
  function startScoreDecrement() {
    gameState.scoreInterval = setInterval(() => {
      if (gameState.isPaused) return // Skip khi pause

      if (!gameState.answered && gameState.timeRemaining > 0) {
        const duration = getCurrentQuestionDuration()
        const scoreDecrement = QUIZ_CONFIG.maxScore / (duration * 10)
        gameState.score -= scoreDecrement
        gameState.score = Math.max(0, gameState.score)
        updateScoreUI()
      }
    }, 100)
  }

  /**
   * Lấy duration của câu hỏi hiện tại
   * @returns {number} Duration (giây), tối thiểu là 1
   */
  function getCurrentQuestionDuration() {
    const question = questions[gameState.currentQuestion]
    return Math.max(1, Number.isFinite(question?.timeLimit)
      ? Number(question.timeLimit)
      : QUIZ_CONFIG.timeLimit)
  }

  // ===== Score UI =====

  /**
   * Cập nhật giao diện điểm số (thanh progress và text)
   */
  function updateScoreUI() {
    const scorePercent = (gameState.score / QUIZ_CONFIG.maxScore) * 100
    $('#score-bar').css('width', scorePercent + '%')
    $('#score-value').text(Math.round(gameState.score))
  }

  // ===== Answer Handling =====

  /**
   * Event handler khi user click vào đáp án
   */
  $(document).on('click', '.answer-btn', function () {
    if (gameState.answered || gameState.isPaused) return

    handleAnswerSelection($(this))
  })

  /**
   * Xử lý khi user chọn một đáp án
   * @param {jQuery} $button - jQuery object của button được click
   */
  function handleAnswerSelection($button) {
    gameState.answered = true
    gameState.selectedAnswer = Number.parseInt($button.attr('data-answer'))

    stopScoreDecrement()
    disableAllAnswers()
    dimUnselectedAnswers()
    scheduleCorrectAnswerReveal()
  }

  /**
   * Dừng việc giảm điểm
   */
  function stopScoreDecrement() {
    if (gameState.scoreInterval) {
      clearInterval(gameState.scoreInterval)
    }
  }

  /**
   * Disable tất cả các button đáp án
   */
  function disableAllAnswers() {
    $('.answer-btn').addClass('disabled')
  }

  /**
   * Làm mờ các đáp án không được chọn
   */
  function dimUnselectedAnswers() {
    $('.answer-btn').each(function () {
      const answerIndex = Number.parseInt($(this).attr('data-answer'))
      if (answerIndex !== gameState.selectedAnswer) {
        $(this).addClass('dimmed')
      }
    })
  }

  /**
   * Lên lịch hiển thị đáp án đúng sau khi hết thời gian còn lại
   */
  function scheduleCorrectAnswerReveal() {
    const timeUntilEnd = gameState.timeRemaining * 1000
    setTimeout(() => {
      if (!gameState.showCorrectAnswerCalled) {
        gameState.showCorrectAnswerCalled = true
        showCorrectAnswer()
      }
    }, timeUntilEnd)
  }

  // ===== Correct Answer Display =====

  /**
   * Hiển thị đáp án đúng sau khi user đã trả lời
   */
  function showCorrectAnswer() {
    stopTimer()

    // Kiểm tra xem user trả lời đúng hay sai và cộng điểm
    const isCorrect = gameState.selectedAnswer === gameState.correctAnswer
    const earnedPoints = isCorrect ? Math.round(gameState.score) : 0

    if (isCorrect) {
      gameState.totalScore += earnedPoints
      console.log(`Correct! Earned ${earnedPoints} points. Total: ${gameState.totalScore}`)
    } else {
      console.log(`Incorrect. Total: ${gameState.totalScore}`)
    }

    // Lưu lịch sử trả lời
    const question = questions[gameState.currentQuestion]
    const timeSpent = getQuestionTimeLimit(question) - gameState.timeRemaining

    gameState.answerHistory.push({
      questionIndex: gameState.currentQuestion,
      selectedAnswer: gameState.selectedAnswer,
      correctAnswer: gameState.correctAnswer,
      isCorrect: isCorrect,
      timeSpent: timeSpent,
      earnedPoints: earnedPoints
    })

    highlightCorrectAnswer()
    handleExplanationAndProceed()
  }

  /**
   * Dừng countdown timer
   */
  function stopTimer() {
    if (gameState.timerInterval) {
      clearInterval(gameState.timerInterval)
    }
  }

  /**
   * Highlight đáp án đúng với checkmark
   */
  function highlightCorrectAnswer() {
    $('.answer-btn').each(function () {
      if (Number.parseInt($(this).attr('data-answer')) === gameState.correctAnswer) {
        $(this)
          .removeClass('dimmed')
          .addClass('show-check')
          .css('opacity', '1')
        $(this).find('.answer-check').css('display', 'flex')
      }
    })
  }

  /**
   * Xử lý hiển thị explanation (nếu có) và chuyển câu tiếp theo
   */
  function handleExplanationAndProceed() {
    const question = questions[gameState.currentQuestion]
    const explanation = getQuestionExplanation(question)

    if (explanation) {
      displayExplanation(explanation)
      setTimeout(proceedToNextQuestion, QUIZ_CONFIG.explanationDuration)
    } else {
      setTimeout(proceedToNextQuestion, QUIZ_CONFIG.noExplanationDelay)
    }
  }

  /**
   * Lấy explanation text từ câu hỏi
   * @param {Object} question - Object câu hỏi
   * @returns {string} Explanation text (đã trim) hoặc empty string
   */
  function getQuestionExplanation(question) {
    return (question && typeof question.explanation === 'string')
      ? question.explanation.trim()
      : ''
  }

  /**
   * Hiển thị explanation overlay trên ảnh (với blur effect)
   * @param {string} explanation - Text explanation cần hiển thị
   */
  function displayExplanation(explanation) {
    const $img = $('#question-image')
    const $expl = $('#explanation-box')
    const $wrap = $img.closest('.image-container')

    $wrap.css('position', 'relative')
    $img.css('filter', 'blur(6px)')

    $expl.css({
      position: 'absolute',
      inset: 0,
      background: 'rgba(0,0,0,0.6)',
      color: '#fff',
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'center',
      textAlign: 'center',
      padding: '12px',
      fontSize: '24px',
    }).text(explanation).fadeIn(QUIZ_CONFIG.explanationFadeInDuration)
  }

  /**
   * Chuyển sang câu hỏi tiếp theo
   */
  function proceedToNextQuestion() {
    gameState.currentQuestion++
    loadQuestion(gameState.currentQuestion)
  }

  // ===== Timeout Handling =====

  /**
   * Xử lý khi hết thời gian mà user chưa trả lời
   */
  function handleTimeOut() {
    gameState.answered = true
    gameState.showCorrectAnswerCalled = true

    // Lưu lịch sử: timeout = không trả lời
    const question = questions[gameState.currentQuestion]
    const timeSpent = getQuestionTimeLimit(question)

    gameState.answerHistory.push({
      questionIndex: gameState.currentQuestion,
      selectedAnswer: null,
      correctAnswer: gameState.correctAnswer,
      isCorrect: false,
      timeSpent: timeSpent,
      earnedPoints: 0
    })

    stopAllIntervals()
    disableAllAnswers()
    highlightCorrectAnswer()
    handleExplanationAndProceed()
  }

  /**
   * Dừng tất cả intervals (timer và score)
   */
  function stopAllIntervals() {
    if (gameState.scoreInterval) clearInterval(gameState.scoreInterval)
    if (gameState.timerInterval) clearInterval(gameState.timerInterval)
  }

  // ===== Quiz End =====

  /**
   * Kết thúc quiz và hiển thị màn hình kết quả
   */
  function endQuiz() {
    console.log(`Quiz Completed! Final Total Score: ${gameState.totalScore}`)

    // Gửi điểm số cuối cùng lên server (nếu cần)
    saveFinalScore()

    // Chuyển sang màn hình kết quả sau 1 giây
    setTimeout(() => {
      showResultsScreen()
    }, 1000)
  }

  /**
   * Hiển thị màn hình kết quả
   */
  function showResultsScreen() {
    // Ẩn container quiz
    $('#quiz-play-container').addClass('hidden')

    // Ẩn các nút pause và next trong header
    $('#pause-btn, #next-btn').hide()

    // Ẩn thông tin player count
    $('#player-count-info').hide()

    // Cập nhật slide info
    const totalQuestions = questions.length
    $('#slide-info').text(`Slide ${totalQuestions}/${totalQuestions}`)

    // Hiển thị điểm số trên màn hình kết quả
    $('#result-score').text(gameState.totalScore)

    // Hiển thị container kết quả
    $('#results-container').addClass('show')

    // Thêm hiệu ứng celebration
    addCelebrationEffect()
  }

  /**
   * Lưu điểm số cuối cùng lên server
   */
  function saveFinalScore() {
    // TODO: Implement AJAX call to save score
    // $.ajax({
    //   url: 'save_score.php',
    //   method: 'POST',
    //   data: { 
    //     score: gameState.totalScore,
    //     session_id: sessionId
    //   },
    //   success: function(response) {
    //     console.log('Score saved successfully', response)
    //   },
    //   error: function(error) {
    //     console.error('Failed to save score:', error)
    //   }
    // })
  }

  /**
   * Thêm hiệu ứng celebration khi hiển thị kết quả
   */
  function addCelebrationEffect() {
    const colors = ['#ffd06f', '#6bcb77', '#8dd7e8', '#ffb8d1']
    const particleCount = 30

    for (let i = 0; i < particleCount; i++) {
      setTimeout(() => {
        createParticle(colors[Math.floor(Math.random() * colors.length)])
      }, i * 100)
    }
  }

  /**
   * Tạo particle celebration
   * @param {string} color - Màu của particle
   */
  function createParticle(color) {
    const $particle = $('<div>')
      .css({
        position: 'fixed',
        width: '10px',
        height: '10px',
        background: color,
        borderRadius: '50%',
        pointerEvents: 'none',
        zIndex: 1000,
        left: Math.random() * window.innerWidth + 'px',
        top: '-20px',
        boxShadow: `0 0 10px ${color}`
      })
      .appendTo('body')

    const duration = 3000 + Math.random() * 2000
    const endX = Math.random() * window.innerWidth
    const endY = window.innerHeight + 50

    $particle.animate({
      top: endY + 'px',
      left: endX + 'px',
      opacity: 0
    }, duration, 'linear', function () {
      $(this).remove()
    })
  }

  // ===== Report Screen =====

  /**
   * Hiển thị màn hình report chi tiết
   */
  function showReportScreen() {
    // Ẩn results container
    $('#results-container').removeClass('show')

    // Render report table
    renderReportTable()

    // Hiển thị report container
    $('#report-container').addClass('show')

    // Setup download and print handlers
    setupReportActions()
  }

  /**
   * Quay lại màn hình results (leaderboard)
   */
  function backToResultsScreen() {
    // Ẩn report container
    $('#report-container').removeClass('show')

    // Hiển thị lại results container
    $('#results-container').addClass('show')
  }

  /**
   * Render bảng report với kết quả từng câu hỏi
   */
  function renderReportTable() {
    const totalQuestions = questions.length
    const correctAnswers = gameState.answerHistory.filter(h => h.isCorrect).length
    const accuracy = totalQuestions > 0 ? Math.round((correctAnswers / totalQuestions) * 100) : 0

    // Render question headers (Q1, Q2, Q3, ...) as separate <th> so alignment matches tbody
    const $headerRow = $('#report-header-row')
    // Remove any previously appended question headers to avoid duplicates
    $headerRow.find('th.col-question').remove()
    for (let i = 0; i < totalQuestions; i++) {
      $headerRow.append(`<th class="col-question">Q${i + 1}</th>`)
    }

    // Render player row
    const playerAvatar = $('#result-avatar').attr('src')
    const playerName = $('#result-name').text()
    const playerScore = gameState.totalScore

    let questionCells = ''
    for (let i = 0; i < totalQuestions; i++) {
      const history = gameState.answerHistory[i]
      if (history) {
        const isCorrect = history.isCorrect
        const iconClass = isCorrect ? 'correct' : 'incorrect'
        const icon = isCorrect ? '✓' : '✕'
        const timeText = `${history.timeSpent.toFixed(1)}s`

        questionCells += `
          <td class="question-result">
            <div class="result-icon ${iconClass}">${icon}</div>
            <span class="result-time">${timeText}</span>
          </td>
        `
      } else {
        questionCells += `
          <td class="question-result">
            <div class="result-icon incorrect">✕</div>
            <span class="result-time">-</span>
          </td>
        `
      }
    }

    const playerRow = `
      <tr>
        <td class="col-player">
          <div class="player-info">
            <img src="${playerAvatar}" alt="${playerName}" class="player-info-avatar">
            <span class="player-info-name">${playerName}</span>
          </div>
        </td>
        <td class="score-cell">${playerScore}</td>
        <td>
          <div class="accuracy-cell">
            <i class="fas fa-check accuracy-icon"></i>
            <span class="accuracy-text">${accuracy}%</span>
          </div>
        </td>
        ${questionCells}
      </tr>
    `

    $('#report-table-body').html(playerRow)
  }

  /**
   * Setup handlers cho download CSV và print
   */
  function setupReportActions() {
    // Download CSV
    $('#download-csv').off('click').on('click', function (e) {
      e.preventDefault()
      downloadReportCSV()
    })

    // Print
    $('#print-report').off('click').on('click', function (e) {
      e.preventDefault()
      window.print()
    })
  }

  /**
   * Download report dưới dạng CSV
   */
  function downloadReportCSV() {
    const playerName = $('#result-name').text()
    const totalQuestions = questions.length
    const correctAnswers = gameState.answerHistory.filter(h => h.isCorrect).length
    const accuracy = totalQuestions > 0 ? Math.round((correctAnswers / totalQuestions) * 100) : 0

    // CSV Header
    let csv = 'Player name,Score,Accuracy'
    for (let i = 0; i < totalQuestions; i++) {
      csv += `,Q${i + 1}`
    }
    csv += '\n'

    // CSV Data
    csv += `"${playerName}",${gameState.totalScore},${accuracy}%`
    for (let i = 0; i < totalQuestions; i++) {
      const history = gameState.answerHistory[i]
      if (history) {
        const result = history.isCorrect ? '✓' : '✕'
        csv += `,${result} (${history.timeSpent.toFixed(1)}s)`
      } else {
        csv += ',✕'
      }
    }
    csv += '\n'

    // Create download link
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
    const link = document.createElement('a')
    const url = URL.createObjectURL(blob)

    link.setAttribute('href', url)
    link.setAttribute('download', `quiz_report_${playerName}_${Date.now()}.csv`)
    link.style.visibility = 'hidden'

    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }

  // ===== Utilities =====

  /**
   * Chuẩn hóa đáp án đúng về dạng number (0-3)
   * Hỗ trợ input: number, "A"/"B"/"C"/"D", hoặc string number
   * @param {number|string} ans - Đáp án cần normalize
   * @returns {number|null} Index của đáp án (0-3) hoặc null nếu invalid
   */
  function normalizeCorrectAnswer(ans) {
    if (ans == null) return null

    if (typeof ans === 'number' && Number.isFinite(ans)) {
      return ans
    }

    if (typeof ans === 'string') {
      const trimmed = ans.trim().toUpperCase()
      const letterToIndexMap = { 'A': 0, 'B': 1, 'C': 2, 'D': 3 }

      if (trimmed in letterToIndexMap) {
        return letterToIndexMap[trimmed]
      }

      const numValue = Number.parseInt(trimmed)
      if (Number.isFinite(numValue)) {
        return numValue
      }
    }

    return null
  }

  // ===== Start Game =====
  init()
})