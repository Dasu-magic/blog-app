document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form");

    forms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            const title = form.querySelector('input[name="title"]');
            const content = form.querySelector('textarea[name="content"]');
            const email = form.querySelector('input[name="email"]');
            const password = form.querySelector('input[name="password"]');
            const username = form.querySelector('input[name="username"]');

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

            if (username && username.value.trim().length < 3) {
                alert("Username must be at least 3 characters.");
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

    const liveClock = document.getElementById("live-clock");
    if (liveClock) {
        const updateClock = function () {
            const now = new Date();
            const timeText = now.toLocaleTimeString([], { hour: "numeric", minute: "2-digit" });
            liveClock.textContent = "Updated at " + timeText;
        };

        updateClock();
        setInterval(updateClock, 30000);
    }

    const metricCards = document.querySelectorAll("[data-target]");
    metricCards.forEach(function (element) {
        const target = Number(element.dataset.target) || 0;
        let current = 0;
        const step = Math.max(1, Math.ceil(target / 40));

        const tick = function () {
            current += step;
            if (current >= target) {
                current = target;
                element.textContent = current.toLocaleString();
                return;
            }
            element.textContent = current.toLocaleString();
            requestAnimationFrame(tick);
        };

        requestAnimationFrame(tick);
    });
});