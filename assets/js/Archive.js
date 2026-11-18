console.log("🚀 Archive.js Loaded!");

// Dropdown functionality
document.addEventListener('click', function (event) {
    const dropdowns = document.querySelectorAll('.dropdown-menu');
    dropdowns.forEach(function (dropdown) {
        if (!dropdown.parentElement.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
});

// Fetch and display archived files
function fetchArchivedFiles() {
    let archivedFileListContainer = document.getElementById("archived-file-list");

    // Check if the container exists
    if (!archivedFileListContainer) {
        console.error("Archived file list container not found.");
        return; // Exit if the container is missing
    }

    fetch('/DocuVaultFinals/phpFunctions/fetch_archived_files.php')
    .then(response => response.json())
    .then(archivedFiles => {
        archivedFileListContainer.innerHTML = "";

        archivedFiles.forEach(file => {
            let archivedFileDiv = document.createElement("div");
            archivedFileDiv.className = "file-item d-flex align-items-center p-2 border rounded mb-2";
            archivedFileDiv.setAttribute("data-id", file.File_ID);
            archivedFileDiv.setAttribute("data-name", file.File);
            archivedFileDiv.setAttribute("data-type", file.File.split('.').pop().toUpperCase());
            archivedFileDiv.setAttribute("data-size", file.Size);
            archivedFileDiv.setAttribute("data-uploader", file.Uploaded_By);

            archivedFileDiv.innerHTML = `
                <span class="me-3"><i class="far fa-file-alt"></i></span>
                <span class="flex-grow-1">${file.File}</span>
                <span class="badge bg-secondary">${file.File.split('.').pop().toUpperCase()}</span>
                <a href="/DocuVaultFinals/uploads/archived/${file.Student_ID}/${file.File}" class="btn btn-link download-btn" download>
                    <i class="fas fa-download"></i>
                </a>
                <div class="dropdown ms-auto">
                    <button class="btn btn-link" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item restore-btn" href="#" data-id="${file.File_ID}" data-file="${file.File}">Restore</a>
                        <a class="dropdown-item delete-btn" href="#" data-id="${file.File_ID}" data-file="${file.File}">Delete</a>
                    </div>
                </div>
            `;

            archivedFileListContainer.appendChild(archivedFileDiv);
        });

        // Now, after the files are added, attach event listeners
        document.querySelectorAll(".restore-btn").forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault();
                const fileID = this.getAttribute("data-id");

                if (!fileID) {
                    alert("File ID missing!");
                    return;
                }

                if (!confirm("Are you sure you want to restore this file?")) return;

                const formData = new FormData();
                formData.append("file_id", fileID);

                fetch("/DocuVaultFinals/phpFunctions/restore_file.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        fetchArchivedFiles(); // Refresh the archived files list
                    }
                })
                .catch(error => console.error("Error restoring file:", error));
            });
        });

        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault();
                const fileID = this.getAttribute("data-id");

                if (!fileID) {
                    alert("File ID missing!");
                    return;
                }

                if (!confirm("Are you sure you want to permanently delete this file?")) return;

                const formData = new FormData();
                formData.append("file_id", fileID);

                fetch("/DocuVaultFinals/phpFunctions/delete_archived_file.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        fetchArchivedFiles(); // Refresh the archived files list
                    }
                })
                .catch(error => console.error("Error deleting file:", error));
            });
        });
    })
    .catch(error => console.error("Error fetching archived files:", error));
}

// Call fetchArchivedFiles to load archived files when the page is ready
document.addEventListener("DOMContentLoaded", function () {
    fetchArchivedFiles(); // Load archived files immediately on page load
});

// File download functionality for regular files
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
        var path = fileElement.getAttribute("data-path") || "";

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
