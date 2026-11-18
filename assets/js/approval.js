console.log("🚀 approval.js Loaded!");

// Dropdown functionality
document.addEventListener('click', function (event) {
    const dropdowns = document.querySelectorAll('.dropdown-menu');
    dropdowns.forEach(function (dropdown) {
        if (!dropdown.parentElement.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
});

// File download functionality
const downloadButtons = document.querySelectorAll('.download-btn');
downloadButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
        event.preventDefault();
        const fileItem = this.closest('.file-item');
        const filePath = fileItem.getAttribute('data-path');
        const fileName = fileItem.getAttribute('data-name');
        const link = document.createElement('a');
        link.href = filePath;
        link.download = fileName;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});

document.getElementById("search-input").addEventListener("input", function() {
    let searchQuery = this.value.trim();

    fetch(`/DocuVaultFinals/phpFunctions/fetch_files.php?search=${encodeURIComponent(searchQuery)}`)
    .then(response => response.json())
    .then(files => {
        let fileListContainer = document.getElementById("file-list");
        fileListContainer.innerHTML = "";

        files.forEach(file => {
            let fileDiv = document.createElement("div");
            fileDiv.className = "file-item d-flex align-items-center p-2 border rounded mb-2";
            fileDiv.setAttribute("data-id", file.File_ID);
            fileDiv.setAttribute("data-name", file.File);
            fileDiv.setAttribute("data-type", file.File.split('.').pop().toUpperCase());
            fileDiv.setAttribute("data-size", file.Size);
            fileDiv.setAttribute("data-uploader", file.Uploaded_By);

            fileDiv.innerHTML = `
                <span class="me-3"><i class="far fa-file-alt"></i></span>
                <span class="flex-grow-1">${file.File}</span>
                <span class="badge bg-secondary">${file.File.split('.').pop().toUpperCase()}</span>
                <a href="/DocuVaultFinals/uploads/approved/${file.Student_ID}/${file.File}" class="btn btn-link download-btn" download>
                    <i class="fas fa-download"></i>
                </a>
                <div class="dropdown ms-auto">
                    <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item archive-btn" href="#" data-id="${file.File_ID}" data-file="${file.File}">Archive</a>
                        <a class="dropdown-item delete-btn" href="#" data-id="${file.File_ID}" data-file="${file.File}">Delete</a>
                    </div>
                </div>
            `;

            fileListContainer.appendChild(fileDiv);
        });
    })
    .catch(error => console.error("Error fetching files:", error));
});

// File item functionality
document.addEventListener("DOMContentLoaded", function () {
    var fileItems = document.querySelectorAll(".file-item");
    var detailsPanel = document.querySelector(".detailscon");

    if (fileItems.length > 0) {
        showFileDetails(fileItems[0]);
    }

    fileItems.forEach(fileItem => {
        fileItem.addEventListener("click", function () {
            showFileDetails(this);
        });
    });

    function showFileDetails(fileElement) {
        var name = fileElement.getAttribute("data-name") || "Unknown";
        var type = (fileElement.getAttribute("data-type") || "Unknown").toUpperCase();
        var size = parseInt(fileElement.getAttribute("data-size")) || 0;
        var uploader = fileElement.getAttribute("data-uploader") || "Unknown";
        var filename = fileElement.getAttribute("data-name");
        var studentId = fileElement.getAttribute("data-id");

        // Search for the actual filename stored in the database
        fetch(`/DocuVaultFinals/phpFunctions/get_actual_filename.php?file_id=${fileElement.getAttribute("data-id")}`)
            .then(response => response.json())
            .then(data => {
                if (data.file_name) {
                    path = `/DocuVaultFinals/uploads/pending/${studentId}/${data.file_name}`;
                    imagePreview.src = path;
                    imagePreview.style.display = "block";
                } else {
                    console.error("File not found in database.");
                }
            })
            .catch(error => console.error("Error fetching filename:", error));


        // Convert file size
        var sizeText = size > 0 
            ? (size >= 1048576 ? (size / (1024 * 1024)).toFixed(2) + " MB" : (size / 1024).toFixed(2) + " KB") 
            : "0 KB";

        // Update details panel
        document.getElementById("name").innerText = name;
        document.getElementById("type").innerText = type;
        document.getElementById("size").innerText = sizeText;
        document.getElementById("uploadedby").innerText = uploader;

        var imagePreview = document.getElementById("image-preview");
        imagePreview.style.display = "none";
        imagePreview.src = "";

        // File type previews
        var fileIcons = {
            "PDF": "../Drawables/pdf-icon.png",
            "TXT": "../Drawables/txt-icon.png",
            "MP4": "../Drawables/video-icon.png",
            "WEBM": "../Drawables/video-icon.png",
            "OGG": "../Drawables/video-icon.png"
        };

        if (["JPG", "JPEG", "PNG", "GIF"].includes(type)) {
            imagePreview.src = path;
            imagePreview.style.display = "block";
        } else if (fileIcons[type]) {
            imagePreview.src = fileIcons[type];
            imagePreview.style.display = "block";
        } else {
            imagePreview.src = "../Drawables/default-icon.png";
            imagePreview.style.display = "block";
        }

        // Show the details panel
        detailsPanel.style.display = "block";
    }
});

$(document).ready(function () {
    $(".approve-btn, .reject-btn").on("click", function (e) {
        e.preventDefault();

        let fileId = $(this).data("id");
        let fileName = $(this).data("file");
        let newStatus = $(this).hasClass("approve-btn") ? "Approved" : "Rejected";

        if (!confirm(`Are you sure you want to mark "${fileName}" as ${newStatus}?`)) return;

        $.ajax({
            type: "POST",
            url: "/DocuVaultFinals/phpFunctions/update_file_status.php",
            data: { file_id: fileId, status: newStatus },
            dataType: "json",
            success: function (response) {
                alert(response.message);
                if (response.status === "success") {
                    // Update the UI by changing the row's appearance
                    let fileRow = $(`.file-item[data-id='${fileId}']`);
                    fileRow.find(".badge").removeClass("bg-secondary").addClass(
                        newStatus === "Approved" ? "bg-success" : "bg-danger"
                    ).text(newStatus.toUpperCase());

                    // Optionally, disable the approve/reject buttons
                    fileRow.find(".approve-btn, .reject-btn").remove();
                }
            },
            error: function () {
                alert("Error updating file status.");
            }
        });
    });
});
