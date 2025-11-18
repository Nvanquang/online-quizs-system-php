// jQuery and Bootstrap from window
const $ = window.$
const bootstrap = window.bootstrap

$(document).ready(() => {
  // Handle cover image upload
  $("#coverInput").change((e) => {
    const file = e.target.files[0]
    if (file) {
      const reader = new FileReader()
      reader.onload = (event) => {
        $("#coverImage").attr("src", event.target.result)
      }
      reader.readAsDataURL(file)
    }
  })

  // Image: open file selector
  $('#qeImageBtn').click(() => {
    $('#qeImageInput').click()
  })

  // Image: handle file selection and preview
  $('#qeImageInput').change((e) => {
    const file = e.target.files && e.target.files[0]
    if (!file) return
    if (!file.type.startsWith('image/')) {
      alert('Please choose a valid image file')
      return
    }
    const reader = new FileReader()
    reader.onload = (ev) => {
      $('#qeImagePreview').attr('src', ev.target.result)
      $('#qeImageContainer').show()
    }
    reader.readAsDataURL(file)
  })

  // Image: remove
  $('#qeImageRemove').click(() => {
    $('#qeImageInput').val('')
    $('#qeImagePreview').attr('src', '')
    $('#qeImageContainer').hide()
  })

  // Add Question button -> open Question Editor modal
  $(".btn-add-question, .btn-purple:first").on('click', () => {
    window.qeMode = 'create'
    window.qeQuestionId = null
    const modalEl = document.getElementById('questionEditorModal')
    if (!modalEl) return
    // Prefill defaults
    $('#qeQuestionText').val('')
    $('#qeAns1, #qeAns2, #qeAns3, #qeAns4').val('')
    $("input[name='qeCorrect']").prop('checked', false)
    // Reset image state
    $('#qeImageInput').val('')
    $('#qeImagePreview').attr('src', '')
    $('#qeImageContainer').hide()
    // Use stored defaults if any
    if (window.editQuizState?.timeLimitSeconds) {
      $('#qeTimeInput').val(window.editQuizState.timeLimitSeconds)
    }
    const modal = new bootstrap.Modal(modalEl)
    modal.show()
  })

  // Edit existing question from the fake cards
  $('.btn-edit-question').click(function () {
    const $btn = $(this)
    window.qeMode = 'edit'
    window.qeQuestionId = $btn.data('question-id') || null
    // Populate fields
    $('#qeQuestionText').val($btn.data('question') || '')
    $('#qeAns1').val($btn.data('ans1') || '')
    $('#qeAns2').val($btn.data('ans2') || '')
    $('#qeAns3').val($btn.data('ans3') || '')
    $('#qeAns4').val($btn.data('ans4') || '')

    // Correct answer
    const correct = $btn.data('correct')
    $("input[name='qeCorrect']").prop('checked', false)
    if (correct) {
      $(`input[name='qeCorrect'][value='${correct}']`).prop('checked', true)
    }

    // Meta
    const time = Number($btn.data('time'))
    $('#qeTimeInput').val(Number.isFinite(time) ? time : (window.editQuizState?.timeLimitSeconds || 20))
    $('#qeType').val($btn.data('type') || 'multiple')
    const random = $btn.data('random')
    $('#qeRandomOrder').prop('checked', random === 1 || random === '1' || random === true)

    // Show modal
    const modalEl = document.getElementById('questionEditorModal')
    if (!modalEl) return
    const modal = new bootstrap.Modal(modalEl)
    // Reset image state, then preload if provided on button
    $('#qeImageInput').val('')
    $('#qeImagePreview').attr('src', '')
    $('#qeImageContainer').hide()
    const imageUrl = $btn.data('image')
    if (imageUrl) {
      $('#qeImagePreview').attr('src', imageUrl)
      $('#qeImageContainer').show()
    }
    modal.show()
  })

  // Save in Question Editor
  $('#qeSaveBtn').click(() => {
    const question = $('#qeQuestionText').val().trim()
    const answers = [
      $('#qeAns1').val().trim(),
      $('#qeAns2').val().trim(),
      $('#qeAns3').val().trim(),
      $('#qeAns4').val().trim(),
    ]
    const correct = $("input[name='qeCorrect']:checked").val() // A/B/C/D
    const timeLimit = parseInt($('#qeTimeInput').val(), 10) || window.editQuizState?.timeLimitSeconds || 20
    // const type = $('#qeType').val()
    // const randomOrder = $('#qeRandomOrder').is(':checked')
    const quizId = parseInt($('#quizId').val(), 10)

    // Validation
    if (!question) {
      alert('Please enter the question text')
      return
    }
    if (answers.some(a => !a)) {
      alert('Please fill in all 4 answers')
      return
    }
    if (!correct) {
      alert('Please select the correct answer')
      return
    }

    // Build FormData
    const fd = new FormData()
    fd.append('quiz_id', isFinite(quizId) ? quizId : '')
    fd.append('time_limit', timeLimit)
    fd.append('content', question)
    fd.append('answer_a', answers[0])
    fd.append('answer_b', answers[1])
    fd.append('answer_c', answers[2])
    fd.append('answer_d', answers[3])
    fd.append('correct_answer', String(correct).toUpperCase())

    const imageFile = document.getElementById('qeImageInput').files && document.getElementById('qeImageInput').files[0]
    if (imageFile) {
      fd.append('image_file', imageFile)
    } else {
      // if editing existing question, try to include the current URL so backend can keep it
      const currentUrl = $('#qeImagePreview').attr('src')
      if (currentUrl) fd.append('image_url', currentUrl)
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

    const mode = window.qeMode === 'edit' ? 'edit' : 'create'
    const qid = window.qeQuestionId
    const url = mode === 'edit' && qid ? `/question/edit/${qid}` : '/question/create'

    $.ajax({
      url,
      method: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      headers: { 'X-CSRF-Token': csrfToken },
      success: (res) => {
        if (res.success) {
          const modal = bootstrap.Modal.getInstance(document.getElementById('questionEditorModal'))
          if (modal) modal.hide()
          toastr.options = { "timeout": 2000 }
          toastr.success(res.message);
          setTimeout(() => {
            location.reload()
          }, 2000)
        }
        else {
          toastr.options = { "timeout": 2000 }
          toastr.error(res.message)
        }
      },
      error: (res) => {
        toastr.options = { "timeout": 2000 }
        toastr.error(res.message || 'Có lỗi xảy ra')
      }
    })
  })

  $('.btn-delete-question').click(function () {
    const qid = $(this).data('question-id') || window.qeQuestionId
    if (!qid) return
    $.ajax({
      url: `/question/delete/${qid}`,
      method: 'POST',
      headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
      success: (res) => {
        if (res.success) {
          toastr.options = { "timeout": 2000 }
          toastr.success(res.message);
          setTimeout(() => {
            location.reload()
          }, 2000)
        }
        else {
          toastr.options = { "timeout": 2000 }
          toastr.error(res.message)
        }
      },
      error: (xhr) => {
        toastr.options = { "timeout": 2000 }
        const response = xhr.responseJSON;
        toastr.error(response?.message || 'Có lỗi xảy ra');
      }
    })
  })

  // Time Limit: open modal
  $('#btnTimeLimit').on('click', () => {
    const modalEl = document.getElementById('timeLimitModal')
    if (!modalEl) return
    // Pre-fill with current value if stored
    if (window.editQuizState?.timeLimitSeconds) {
      $('#timeLimitInput').val(window.editQuizState.timeLimitSeconds)
    }
    const modal = new bootstrap.Modal(modalEl)
    modal.show()
  })

  // Confirm Time Limit
  $(document).on('click', '#confirmTimeLimitBtn', () => {
    const value = parseInt($('#timeLimitInput').val(), 10)
    if (!Number.isFinite(value) || value <= 0) {
      alert('Please enter a valid number of seconds > 0')
      return
    }
    window.editQuizState = window.editQuizState || {}
    window.editQuizState.timeLimitSeconds = value
    console.log('[edit] Time limit set (seconds):', value)
    const modalEl = document.getElementById('timeLimitModal')
    const instance = bootstrap.Modal.getInstance(modalEl)
    if (instance) instance.hide()
  })
})
