document.addEventListener("DOMContentLoaded", function () {

    const forms = document.querySelectorAll("form");

    forms.forEach(function (form) {

        form.addEventListener("submit", function (event) {

            const title = form.querySelector('input[name="title"]');
            const content = form.querySelector('textarea[name="content"]');

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

        });

    });

});