AOS.init({
    duration: 1000,
    once: true,
});

document.addEventListener("DOMContentLoaded", function () {
    const swiper = new Swiper(".hero-slider", {
        loop: true,
        speed: 800,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const target = +el.dataset.target;
                const step = target / 200;
                const update = () => {
                    const value = +el.innerText;
                    if (value < target) {
                        el.innerText = Math.ceil(value + step);
                        setTimeout(update, 10);
                    } else el.innerText = target;
                };
                update();
                observer.unobserve(el);
            });
        },
        { threshold: 0.5 }
    );

    document.querySelectorAll(".counter").forEach((el) => observer.observe(el));
});

document.addEventListener("DOMContentLoaded", function () {
    const countdown = (endDate) => {
        const daysEl = document.getElementById("days");
        const hoursEl = document.getElementById("hours");
        const minutesEl = document.getElementById("minutes");
        const secondsEl = document.getElementById("seconds");

        const updateCountdown = () => {
            const now = new Date().getTime();
            const distance = endDate - now;

            if (distance <= 0) {
                daysEl.innerText = "00";
                hoursEl.innerText = "00";
                minutesEl.innerText = "00";
                secondsEl.innerText = "00";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor(
                (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
            );
            const minutes = Math.floor(
                (distance % (1000 * 60 * 60)) / (1000 * 60)
            );
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            daysEl.innerText = String(days).padStart(2, "0");
            hoursEl.innerText = String(hours).padStart(2, "0");
            minutesEl.innerText = String(minutes).padStart(2, "0");
            secondsEl.innerText = String(seconds).padStart(2, "0");
        };

        updateCountdown(); // run once immediately
        setInterval(updateCountdown, 1000);
    };

    // Cấu hình ngày kết thúc (VD: 10 ngày sau hôm nay)
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + 10);
    countdown(targetDate);
});

const swiper = new Swiper(".testimonialSwiper", {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: true,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        576: {
            slidesPerView: 1.2,
        },
        768: {
            slidesPerView: 2,
        },
        992: {
            slidesPerView: 3,
        },
    },
});

document.addEventListener("DOMContentLoaded", function () {
    new Swiper(".review-swiper", {
        loop: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
    });
});
