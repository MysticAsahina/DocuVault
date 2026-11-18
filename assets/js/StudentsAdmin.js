// JavaScript to Toggle 3-dot Menu
document.querySelectorAll('.dropdown-button').forEach(button => {
    button.addEventListener('click', function (event) {
        event.stopPropagation();
        const dropdownMenu = this.closest('.dropdown').querySelector('.dropdown-menu');
        dropdownMenu.classList.toggle('show');
    });
});

// Close the dropdown if clicked outside
window.addEventListener('click', function (event) {
    if (!event.target.matches('.dropdown-button')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Add Student Form
document.querySelector('.form').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this);

    if (!formData.get('password')) {
        alert('Password is required.');
        return;
    }

    fetch('/DocuVaultFinals/phpFunctions/add_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            window.location.href = 'students.php';
        } else {
            alert(data.message || 'Error adding student.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding student.');
    });
});

// Show Add Student Modal
document.getElementById('plus').addEventListener('click', function () {
    const addModal = new bootstrap.Modal(document.getElementById('myModal'));
    addModal.show();
});

// Edit Student
$(document).on("click", ".edit-btn", function () {
    let studentId = $(this).data("id");

    console.log("Fetching details for student ID:", studentId); // Debugging

    $.ajax({
        type: "POST",
        url: "/DocuVaultFinals/phpFunctions/edit_students.php",
        data: { student_id: studentId },
        dataType: "json",
        success: function (response) {
            if (response.error) {
                alert(response.error);
            } else {
                $("#editModal input[name='student_id']").val(response.Student_ID);
                $("#editModal input[name='first_name']").val(response.First_Name);
                $("#editModal input[name='middle_name']").val(response.Middle_Name);
                $("#editModal input[name='last_name']").val(response.Last_Name);
                $("#editModal input[name='email']").val(response.Email);
                $("#editModal input[name='year_section']").val(response.Year_Section);
                $("#editModal").modal("show");
            }
        },
        error: function () {
            alert("Error fetching student details.");
        },
    });
});

// Handle Edit Form Submission
$(".edit-form").on("submit", function (event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch('/DocuVaultFinals/phpFunctions/edit_students.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating student.');
    });
});

// Delete Student
$(document).on("click", ".delete-btn", function () {
    let studentId = $(this).data("id");

    if (confirm("Are you sure you want to delete this student?")) {
        $.ajax({
            type: "POST",
            url: "/DocuVaultFinals/phpFunctions/delete_student.php",
            data: JSON.stringify({ student_ID: studentId }),
            contentType: "application/json",
            dataType: "json",
            success: function (response) {
                alert(response.message);
                if (response.status === "success") {
                    location.reload();
                }
            },
            error: function () {
                alert("Error deleting student.");
            },
        });
    }
});
