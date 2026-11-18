console.log("🚀 Dashboard.js Loaded!");

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


// Upload form functionality
const uploadBox = document.querySelector('.upload-wrapper');
const toggleUploadBtn = document.getElementById('plus');

toggleUploadBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    uploadBox.style.display = uploadBox.style.display === 'block' ? 'none' : 'block';
});

window.addEventListener('click', (event) => {
    if (event.target !== uploadBox && event.target !== toggleUploadBtn && !uploadBox.contains(event.target)) {
        uploadBox.style.display = 'none';
    }
});

// File upload handling
const inputFile = document.querySelector('#input-file');
const fileNameDisplay = document.querySelector('.file-name');
const fileSizeDisplay = document.querySelector('.file-size');

inputFile.addEventListener("change", (e) => {
    const file = e.target.files[0];
    fileNameDisplay.textContent = file.name;
    fileSizeDisplay.textContent = (file.size / 1024).toFixed(2) + " KB";
});

uploadBox.addEventListener("reset", () => {
    fileNameDisplay.textContent = "No File Uploaded";
    fileSizeDisplay.textContent = "0.00 KB";
});

// Drag & Drop handling
const uploadZone = document.querySelector('.upload-zone');
uploadZone.addEventListener('dragover', (e) => e.preventDefault());
uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    inputFile.files = e.dataTransfer.files;
    fileNameDisplay.textContent = file.name;
    fileSizeDisplay.textContent = (file.size / 1024).toFixed(2) + " KB";
});

document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.getElementById("student-table-body");
    const selectedStudentInput = document.getElementById("selected-student");

    // Add click event listener to table rows
    tableBody.addEventListener("click", function (event) {
        let row = event.target.closest("tr");
        if (!row) return;

        // Remove 'selected' class from all rows
        document.querySelectorAll("#student-table-body tr").forEach(r => r.classList.remove("selected"));

        // Add 'selected' class to clicked row
        row.classList.add("selected");

        // Store selected student ID
        selectedStudentInput.value = row.cells[0].textContent.trim(); // Assuming ID is in the first cell
    });

    // Handle file upload
    document.querySelector(".upload-wrapper").addEventListener("submit", function (e) {
        e.preventDefault();

        const fileInput = document.getElementById("input-file");
        const selectedStudent = selectedStudentInput.value;

        if (!fileInput.files.length || !selectedStudent) {
            alert("Please select a student and choose a file.");
            return;
        }

        let formData = new FormData();
        formData.append("file", fileInput.files[0]);
        formData.append("student_ID", selectedStudent);

        fetch("/DocuVaultFinals/phpFunctions/upload.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Server Response:", data);
            if (data.status === "success") {
                alert("✅ Upload Successful!");
            } else {
                alert("❌ Upload Failed: " + data.message);
            }
        })
        .catch(error => console.error("Upload Error:", error));
    });
});


// Student search functionality
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("student-search");
    const studentTableBody = document.getElementById("student-table-body");

    fetch('/DocuVaultFinals/phpFunctions/fetch_students.php')
        .then(response => response.json())
        .then(data => {
            studentTableBody.innerHTML = data.map(student => `
                <tr data-student-id="${student.student_ID}">
                    <td>${student.student_ID}</td>
                    <td>${student.last_name}</td>
                    <td>${student.first_name}</td>
                </tr>
            `).join(""); 
        })
        .catch(error => console.error("Fetch error:", error));

    searchInput.addEventListener("input", function () {
        const searchValue = searchInput.value.toLowerCase();
        document.querySelectorAll("#student-table-body tr").forEach(row => {
            const studentID = row.cells[0].innerText.toLowerCase();
            const lastName = row.cells[1].innerText.toLowerCase();
            const firstName = row.cells[2].innerText.toLowerCase();
            row.style.display = (studentID.includes(searchValue) || lastName.includes(searchValue) || firstName.includes(searchValue))
                ? "" : "none";
        });
    });

    studentTableBody.addEventListener("click", function (event) {
        const row = event.target.closest("tr");
        if (!row) return;
        const studentID = row.getAttribute("data-student-id");
        document.getElementById("selected-student").value = studentID;
        alert(`Selected Student: ${studentID}`);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".archive-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            event.preventDefault();

            const fileID = this.getAttribute("data-id");
            console.log("File ID: ", fileID)
            if (!fileID) {
                alert("File ID missing!");
                return;
            }

            if (!confirm("Are you sure you want to archive this file?")) return;

            const formData = new FormData();
            formData.append("file_id", fileID);

            fetch("/DocuVaultFinals/phpFunctions/archive_file.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    this.closest(".file-item").remove(); 
                }
            })
            .catch(error => console.error("Error:", error));
        });
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

        if (!confirm("Are you sure you want to delete this file?")) return;

        // Create a FormData object and append the file_id
        const formData = new FormData();
        formData.append("file_id", fileID);

        fetch("/DocuVaultFinals/phpFunctions/delete_file.php", {
            method: "POST",
            body: formData 
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error("Error:", error));
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

document.addEventListener("DOMContentLoaded", function () {
    const tableRows = document.querySelectorAll("#student-table-body tr");

    tableRows.forEach(row => {
        row.addEventListener("click", function () {
            // Remove 'selected' class from other rows
            tableRows.forEach(r => r.classList.remove("selected"));
            // Add 'selected' class to the clicked row
            row.classList.add("selected");
        });
    });
});