const $ = window.$

$(document).ready(() => {
  // Quiz Configuration
  const QUIZ_CONFIG = {
    timeLimit: 10,
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
    totalScore: 0,
    timeRemaining: QUIZ_CONFIG.timeLimit,
    answered: false,
    selectedAnswer: null,
    correctAnswer: null,
    timerInterval: null,
    scoreInterval: null,
    showCorrectAnswerCalled: false,
    typingInterval: null,
    isPaused: false,
    pausedTimeRemaining: 0,
    pausedScore: 0,
    answerHistory: [],
    rating: null, // Lưu đánh giá số sao
  }

  // ===== Initialization =====
  function init() {
    setupPauseButton()
    setupNextButton()
    setupFullscreenButton()
    setupReportButton()
    setupLeaderboardButton()
    setupDoneButton()
    setupRatingModal()
    loadQuestion(gameState.currentQuestion)
  }

  // ===== Rating Modal Setup =====
  function setupRatingModal() {
    // Click vào star để đánh giá
    $(document).on('click', '.star-btn', function() {
      const rating = parseInt($(this).attr('data-rating'))
      gameState.rating = rating
      
      // Highlight các star đã chọn
      $('.star-btn').each(function(index) {
        const $star = $(this).find('i')
        if (index < rating) {
          $star.removeClass('far').addClass('fas')
        } else {
          $star.removeClass('fas').addClass('far')
        }
      })
      
      // Đóng modal sau 500ms
      setTimeout(() => {
        closeRatingModal()
      }, 500)
    })
    
    // Click cancel để đóng modal
    $('#rating-cancel-btn').on('click', function() {
      closeRatingModal()
    })
    
    // Click overlay để đóng modal
    $('#rating-modal-overlay').on('click', function(e) {
      if (e.target === this) {
        closeRatingModal()
      }
    })
  }

  function showRatingModal() {
    // Lấy tên quiz từ header
    const quizTitle = $('.quiz-name').text() || 'This Quiz'
    $('#quiz-title-rating').text(quizTitle)
    
    // Reset stars về trạng thái chưa chọn
    $('.star-btn i').removeClass('fas').addClass('far')
    
    // Hiển thị modal với animation
    const $overlay = $('#rating-modal-overlay')
    $overlay.fadeIn(300)
    $('.rating-modal').css('animation', 'modalSlideIn 0.4s ease-out')
  }

  function closeRatingModal() {
    $('#rating-modal-overlay').fadeOut(300)
  }

  // ===== Pause/Resume Functionality =====
  function setupPauseButton() {
    $('#pause-btn').on('click', function () {
      if (gameState.isPaused) {
        resumeGame()
      } else {
        pauseGame()
      }
    })
  }

  function setupNextButton() {
    $('#next-btn').on('click', function () {
      skipCurrentQuestion()
    })
  }

  function setupFullscreenButton() {
    $('#fullscreen-btn').on('click', function () {
      toggleFullscreen()
    })
  }

  function setupReportButton() {
    $('#view-report-btn').on('click', function () {
      showReportScreen()
    })
  }

  function setupLeaderboardButton() {
    $('#view-leaderboard-btn').on('click', function () {
      backToResultsScreen()
    })
  }

  function setupDoneButton() {
    $('#done-btn').on('click', async function () {
      const $btn = $(this)
      if ($btn.prop('disabled')) return

      try {
        $btn.prop('disabled', true).addClass('disabled').text('Submitting...')
        const payload = buildEndGamePayload()
        await postEndGame(payload)
        window.location.href = '/'
      } catch (err) {
        console.error('Submit end game failed:', err)
        alert('Failed to submit results. Please try again.')
        $btn.prop('disabled', false).removeClass('disabled').html('<i class="fas fa-check"></i> Done')
      }
    })
  }

  function buildEndGamePayload() {
    const total_questions = questions.length
    const correct_answers = gameState.answerHistory.filter(h => h.isCorrect).length

    return {
      session_code: (window.SESSION_CODE || '').toString(),
      id: Number(window.SESSION_PLAYER_ID) || null,
      total_score: Number(gameState.totalScore),
      total_questions: Number(total_questions),
      correct_answers: Number(correct_answers),
      rating: gameState.rating || null, // Thêm rating vào payload
    }
  }

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

  document.addEventListener('fullscreenchange', function () {
    const $btn = $('#fullscreen-btn')
    if (document.fullscreenElement) {
      $btn.find('i').removeClass('fa-expand').addClass('fa-compress')
    } else {
      $btn.find('i').removeClass('fa-compress').addClass('fa-expand')
    }
  })

  function pauseGame() {
    if (gameState.answered || gameState.isPaused) return

    gameState.isPaused = true
    gameState.pausedTimeRemaining = gameState.timeRemaining
    gameState.pausedScore = gameState.score

    if (gameState.timerInterval) clearInterval(gameState.timerInterval)
    if (gameState.scoreInterval) clearInterval(gameState.scoreInterval)
    if (gameState.typingInterval) clearInterval(gameState.typingInterval)

    const $pauseBtn = $('#pause-btn')
    $pauseBtn.attr('title', 'Resume')
    $pauseBtn.find('i').removeClass('fa-pause').addClass('fa-play')

    $('.answer-btn').addClass('disabled')
  }

  function resumeGame() {
    if (!gameState.isPaused) return

    const question = questions[gameState.currentQuestion]
    if (!question) return

    gameState.isPaused = false
    gameState.timeRemaining = gameState.pausedTimeRemaining
    gameState.score = gameState.pausedScore

    $('#question-text').text(question.text || '')

    const imageSrc = question.image || ''
    const $img = $('#question-image')
    if (imageSrc) {
      setupImageLoadHandlers($img, imageSrc, () => { })
    } else {
      $img.hide()
    }

    const $answerBtns = $('.answer-btn')
    fillAnswerButtons($answerBtns, question)
    $answerBtns.show()

    const $pauseBtn = $('#pause-btn')
    $pauseBtn.attr('title', 'Pause')
    $pauseBtn.find('i').removeClass('fa-play').addClass('fa-pause')

    if (!gameState.answered) {
      $('.answer-btn').removeClass('disabled')
    }

    startQuestionTimer()
    updateScoreUI()
  }

  function skipCurrentQuestion() {
    if (gameState.isPaused) {
      resumeGame()
    }

    gameState.answered = true
    gameState.showCorrectAnswerCalled = true

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

    stopAllIntervals()

    gameState.currentQuestion++
    loadQuestion(gameState.currentQuestion)
  }

  // ===== Question Loading =====
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

  function updateSlideInfo(index) {
    $('#slide-info').text(`Slide ${index + 1}/${questions.length}`)
  }

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

  function getQuestionTimeLimit(question) {
    return Number.isFinite(question?.timeLimit) && question.timeLimit > 0
      ? Number(question.timeLimit)
      : QUIZ_CONFIG.timeLimit
  }

  function clearIntervals() {
    if (gameState.timerInterval) clearInterval(gameState.timerInterval)
    if (gameState.scoreInterval) clearInterval(gameState.scoreInterval)
    if (gameState.typingInterval) clearInterval(gameState.typingInterval)
  }

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

  function getTypingSpeed() {
    if (typeof window !== 'undefined' && typeof window.TYPE_SPEED_MS === 'number') {
      return window.TYPE_SPEED_MS
    }
    return typeof QUIZ_CONFIG.typingSpeedMs === 'number' ? QUIZ_CONFIG.typingSpeedMs : 35
  }

  // ===== Image Loading =====
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
  function revealAnswersAndStartTimer(question) {
    const $answerBtns = $('.answer-btn')

    fillAnswerButtons($answerBtns, question)
    staggerAnswerReveal($answerBtns)
    scheduleTimerStart($answerBtns)
  }

  function fillAnswerButtons($answerBtns, question) {
    $answerBtns.each(function (i) {
      const answerText = Array.isArray(question.answers) ? (question.answers[i] || '') : ''
      $(this).find('.answer-text').text(answerText)
      $(this).attr('data-answer', i)
    })
  }

  function staggerAnswerReveal($answerBtns) {
    $answerBtns.each(function (idx) {
      const btn = this
      setTimeout(() => {
        $(btn).fadeIn(QUIZ_CONFIG.answerFadeInDuration)
      }, idx * QUIZ_CONFIG.answerRevealDelay)
    })
  }

  function scheduleTimerStart($answerBtns) {
    const delay = $answerBtns.length
      ? ($answerBtns.length - 1) * QUIZ_CONFIG.answerRevealDelay + QUIZ_CONFIG.timerStartDelay
      : 0

    setTimeout(() => {
      startQuestionTimer()
    }, delay)
  }

  // ===== Timers =====
  function startQuestionTimer() {
    if (gameState.isPaused) return

    startCountdownTimer()
    startScoreDecrement()
  }

  function startCountdownTimer() {
    gameState.timerInterval = setInterval(() => {
      if (gameState.isPaused) return

      gameState.timeRemaining--

      updateTimerDisplay()

      if (gameState.timeRemaining <= 0) {
        clearInterval(gameState.timerInterval)
        if (!gameState.answered) {
          handleTimeOut()
        }
      }
    }, 1000)
  }

  function updateTimerDisplay() {
    // Có thể thêm hiển thị countdown trên UI nếu muốn
  }

  function startScoreDecrement() {
    gameState.scoreInterval = setInterval(() => {
      if (gameState.isPaused) return

      if (!gameState.answered && gameState.timeRemaining > 0) {
        const duration = getCurrentQuestionDuration()
        const scoreDecrement = QUIZ_CONFIG.maxScore / (duration * 10)
        gameState.score -= scoreDecrement
        gameState.score = Math.max(0, gameState.score)
        updateScoreUI()
      }
    }, 100)
  }

  function getCurrentQuestionDuration() {
    const question = questions[gameState.currentQuestion]
    return Math.max(1, Number.isFinite(question?.timeLimit)
      ? Number(question.timeLimit)
      : QUIZ_CONFIG.timeLimit)
  }

  // ===== Score UI =====
  function updateScoreUI() {
    const scorePercent = (gameState.score / QUIZ_CONFIG.maxScore) * 100
    $('#score-bar').css('width', scorePercent + '%')
    $('#score-value').text(Math.round(gameState.score))
  }

  // ===== Answer Handling =====
  $(document).on('click', '.answer-btn', function () {
    if (gameState.answered || gameState.isPaused) return

    handleAnswerSelection($(this))
  })

  function handleAnswerSelection($button) {
    gameState.answered = true
    gameState.selectedAnswer = Number.parseInt($button.attr('data-answer'))

    stopScoreDecrement()
    disableAllAnswers()
    dimUnselectedAnswers()
    scheduleCorrectAnswerReveal()
  }

  function stopScoreDecrement() {
    if (gameState.scoreInterval) {
      clearInterval(gameState.scoreInterval)
    }
  }

  function disableAllAnswers() {
    $('.answer-btn').addClass('disabled')
  }

  function dimUnselectedAnswers() {
    $('.answer-btn').each(function () {
      const answerIndex = Number.parseInt($(this).attr('data-answer'))
      if (answerIndex !== gameState.selectedAnswer) {
        $(this).addClass('dimmed')
      }
    })
  }

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
  function showCorrectAnswer() {
    stopTimer()

    const isCorrect = gameState.selectedAnswer === gameState.correctAnswer
    const earnedPoints = isCorrect ? Math.round(gameState.score) : 0

    if (isCorrect) {
      gameState.totalScore += earnedPoints
      console.log(`Correct! Earned ${earnedPoints} points. Total: ${gameState.totalScore}`)
    } else {
      console.log(`Incorrect. Total: ${gameState.totalScore}`)
    }

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

  function stopTimer() {
    if (gameState.timerInterval) {
      clearInterval(gameState.timerInterval)
    }
  }

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

  function getQuestionExplanation(question) {
    return (question && typeof question.explanation === 'string')
      ? question.explanation.trim()
      : ''
  }

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

  function proceedToNextQuestion() {
    gameState.currentQuestion++
    loadQuestion(gameState.currentQuestion)
  }

  // ===== Timeout Handling =====
  function handleTimeOut() {
    gameState.answered = true
    gameState.showCorrectAnswerCalled = true

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

  function stopAllIntervals() {
    if (gameState.scoreInterval) clearInterval(gameState.scoreInterval)
    if (gameState.timerInterval) clearInterval(gameState.timerInterval)
  }

  // ===== Quiz End =====
  function endQuiz() {
    console.log(`Quiz Completed! Final Total Score: ${gameState.totalScore}`)

    saveFinalScore()

    setTimeout(() => {
      showResultsScreen()
    }, 1000)
  }

  function showResultsScreen() {
    $('#quiz-play-container').addClass('hidden')

    $('#pause-btn, #next-btn').hide()

    $('#player-count-info').hide()

    const totalQuestions = questions.length
    $('#slide-info').text(`Slide ${totalQuestions}/${totalQuestions}`)

    $('#result-score').text(gameState.totalScore)

    $('#results-container').addClass('show')

    addCelebrationEffect()
    
    // Hiển thị rating modal sau 1 giây
    setTimeout(() => {
      showRatingModal()
    }, 1000)
  }

  function saveFinalScore() {
    // TODO: Implement if needed
  }

  function addCelebrationEffect() {
    const colors = ['#ffd06f', '#6bcb77', '#8dd7e8', '#ffb8d1']
    const particleCount = 30

    for (let i = 0; i < particleCount; i++) {
      setTimeout(() => {
        createParticle(colors[Math.floor(Math.random() * colors.length)])
      }, i * 100)
    }
  }

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
  function showReportScreen() {
    $('#results-container').removeClass('show')

    renderReportTable()

    $('#report-container').addClass('show')

    setupReportActions()
  }

  function backToResultsScreen() {
    $('#report-container').removeClass('show')

    $('#results-container').addClass('show')
  }

  function renderReportTable() {
    const totalQuestions = questions.length
    const correctAnswers = gameState.answerHistory.filter(h => h.isCorrect).length
    const accuracy = totalQuestions > 0 ? Math.round((correctAnswers / totalQuestions) * 100) : 0

    const $headerRow = $('#report-header-row')
    $headerRow.find('th.col-question').remove()
    for (let i = 0; i < totalQuestions; i++) {
      $headerRow.append(`<th class="col-question">Q${i + 1}</th>`)
    }

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

  function setupReportActions() {
    $('#download-csv').off('click').on('click', function (e) {
      e.preventDefault()
      downloadReportCSV()
    })

    $('#print-report').off('click').on('click', function (e) {
      e.preventDefault()
      window.print()
    })
  }

  function downloadReportCSV() {
    const playerName = $('#result-name').text()
    const totalQuestions = questions.length
    const correctAnswers = gameState.answerHistory.filter(h => h.isCorrect).length
    const accuracy = totalQuestions > 0 ? Math.round((correctAnswers / totalQuestions) * 100) : 0

    let csv = 'Player name,Score,Accuracy'
    for (let i = 0; i < totalQuestions; i++) {
      csv += `,Q${i + 1}`
    }
    csv += '\n'

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