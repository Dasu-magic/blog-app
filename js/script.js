document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form");

    forms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            const title = form.querySelector('input[name="title"]');
            const content = form.querySelector('textarea[name="content"]');
            const email = form.querySelector('input[name="email"]');
            const password = form.querySelector('input[name="password"]');
            const name = form.querySelector('input[name="name"]');

            if (title && title.value.trim().length < 3) {
                alert("Blog title must be at least 3 characters.");
                event.preventDefault();
                return;
            }

            if (content && content.value.trim().length < 10) {
                alert("Blog content must be at least 10 characters.");
                event.preventDefault();
                return;
            }

            if (name && name.value.trim().length < 2) {
                alert("Write must be at least 2 characters.");
                event.preventDefault();
                return;
            }

            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
                alert("Please enter a valid email address.");
                event.preventDefault();
                return;
            }

            if (password && password.value.trim().length < 6) {
                alert("Password must be at least 6 characters long.");
                event.preventDefault();
            }
        });
    });

    const deleteLinks = document.querySelectorAll(".delete-blog");
    deleteLinks.forEach(function (link) {
        link.addEventListener("click", function (event) {
            const confirmed = confirm("Are you sure you want to delete this blog?");
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

});