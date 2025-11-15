const $ = window.$
const bootstrap = window.bootstrap // Declare the bootstrap variable

$(document).ready(() => {
  // Initialize drag and drop
  initDragAndDrop()
  initFileHandlers()
  initRemoveOverlay()
  initMethodButtons()
  initCreateButton()
})

// Drag and Drop Handler
function initDragAndDrop() {
  const dropZone = $("#dropZone")

  dropZone.on("dragover", (e) => {
    e.preventDefault()
    e.stopPropagation()
    dropZone.closest(".upload-area").addClass("dragover")
  })

  dropZone.on("dragleave", (e) => {
    e.preventDefault()
    e.stopPropagation()
    dropZone.closest(".upload-area").removeClass("dragover")
  })

  dropZone.on("drop", (e) => {
    e.preventDefault()
    e.stopPropagation()
    dropZone.closest(".upload-area").removeClass("dragover")

    const files = e.originalEvent.dataTransfer.files
    if (files.length > 0) {
      handleFileUpload(files[0])
    }
  })
}

// File Input Handlers
function initFileHandlers() {
  $("#fileUpload").on("change", function () {
    if (this.files.length > 0) {
      handleFileUpload(this.files[0])
    }
  })
}

// Remove image overlay click
function initRemoveOverlay() {
  $(document).on('click', '#removeImageBtn', function () {
    const previewImage = $("#previewImage")
    const uploadContent = $("#uploadContent")
    const dropZone = $("#dropZone")
    // Reset selected sources
    window.selectedImageFile = null
    window.selectedImageUrl = null
    // Mark that existing cover (if any) was removed
    window.removedExistingCover = true
    // UI updates
    previewImage.attr('src', '/placeholder.svg').addClass('d-none')
    uploadContent.removeClass('d-none')
    dropZone.removeClass('has-image')
    // Clear file input selection if any
    const fileInput = document.getElementById('fileUpload')
    if (fileInput) fileInput.value = ''
  })
}

// Handle File Upload
function handleFileUpload(file) {
  if (!file.type.startsWith("image/")) {
    alert("Please select an image file")
    return
  }

  const reader = new FileReader()

  reader.onload = (e) => {
    const previewImage = $("#previewImage")
    const uploadContent = $("#uploadContent")

    previewImage.attr("src", e.target.result).removeClass("d-none")
    uploadContent.addClass("d-none")
    $("#dropZone").addClass("has-image")

    // Remember selected file and clear URL state
    window.selectedImageFile = file
    window.selectedImageUrl = null
    window.removedExistingCover = false
  }

  reader.readAsDataURL(file)
}

// Load Image from URL
window.loadImageFromUrl = () => {
  const url = $("#urlInput").val()

  if (!url) {
    alert("Please enter a URL")
    return
  }

  const previewImage = $("#previewImage")
  const uploadContent = $("#uploadContent")

  previewImage.attr("src", url).removeClass("d-none")
  uploadContent.addClass("d-none")
  $("#dropZone").addClass("has-image")

  // Remember URL and clear file state
  window.selectedImageUrl = url
  window.selectedImageFile = null

  // Close modal
  bootstrap.Modal.getInstance(document.getElementById("urlModal")).hide()
  $("#urlInput").val("")
}

// Creation Method Button Handler
function initMethodButtons() {
  $(".btn-method").on("click", function () {
    $(".btn-method").removeClass("active")
    $(this).addClass("active")
  })
}

// Initialize Create Button
function initCreateButton() {
  $("#createBtn").on("click", () => {
    const title = $("#titleInput").val().trim()

    if (!title) {
      alert("Please enter a title for your question set")
      return
    }

    const isPublic = $("#privacyToggle").is(":checked")

    // Build FormData for POST
    const fd = new FormData()
    fd.append("title", title)
    fd.append("is_public", isPublic ? "1" : "0")

    // Attach image if present
    if (window.selectedImageFile) {
      fd.append("image", window.selectedImageFile)
    }

    // CSRF token from hidden input rendered by server
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || ""
    if (csrfToken) {
      fd.append("csrf_token", csrfToken)
    }

    // Disable button to prevent double submit
    const $btn = $("#createBtn").prop("disabled", true)

    // Build a temporary form for standard submission
    const form = document.createElement('form')
    const action = window.QUIZ_FORM_ACTION || '/quiz/create'
    form.action = action
    form.method = 'POST'
    form.enctype = 'multipart/form-data'

    // Helper to add hidden input
    const addHidden = (name, value) => {
      const input = document.createElement('input')
      input.type = 'hidden'
      input.name = name
      input.value = value
      form.appendChild(input)
    }

    addHidden('title', title)
    addHidden('is_public', isPublic ? '1' : '0')

    // CSRF token
    if (csrfToken) addHidden('csrf_token', csrfToken)

    // Move the existing file input (so the selected file is included)
    const fileInput = document.getElementById('fileUpload')
    if (fileInput && fileInput.files && fileInput.files.length > 0) {
      fileInput.name = 'image'
      form.appendChild(fileInput)
    }

    // If user removed existing cover (when editing), inform server
    if (window.removedExistingCover) {
      addHidden('remove_cover', '1')
    }

    // Submit the form
    document.body.appendChild(form)
    form.submit()
  })
}


