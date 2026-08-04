$(function () {
    // ==== KONFIGURASI ====
    const SESSION_LIFETIME =
        parseInt($('meta[name="session-lifetime"]').attr("content")) || 7200; // detik
    const WARNING_BEFORE = 60; // tampilkan warning 60 detik sebelum session habis
    const PING_URL = "/session/ping";
    const LOGOUT_URL = "/session/logout";
    const LOGIN_URL = "/login";

    let warningTimer, logoutTimer, countdownInterval;
    let countdownValue = WARNING_BEFORE;

    function resetTimers() {
        clearTimeout(warningTimer);
        clearTimeout(logoutTimer);
        clearInterval(countdownInterval);
        countdownValue = WARNING_BEFORE;

        const warningDelay = (SESSION_LIFETIME - WARNING_BEFORE) * 1000;

        warningTimer = setTimeout(showWarning, warningDelay);
    }

    function showWarning() {
        countdownValue = WARNING_BEFORE;

        Swal.fire({
            title: "Sesi Anda akan segera berakhir",
            html: `Sesi akan otomatis logout dalam <b>${countdownValue}</b> detik.<br>Ingin melanjutkan sesi?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Lanjutkan Sesi",
            cancelButtonText: "Logout Sekarang",
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: WARNING_BEFORE * 1000,
            timerProgressBar: true,
            didOpen: () => {
                const htmlContainer = Swal.getHtmlContainer();
                countdownInterval = setInterval(() => {
                    countdownValue--;
                    const bTag = htmlContainer.querySelector("b");
                    if (bTag) bTag.textContent = countdownValue;
                    if (countdownValue <= 0) clearInterval(countdownInterval);
                }, 1000);
            },
        }).then((result) => {
            clearInterval(countdownInterval);

            if (result.isConfirmed) {
                extendSession();
            } else {
                doLogout();
            }
        });

        // Kalau timer habis tanpa interaksi apapun -> auto logout
        logoutTimer = setTimeout(() => {
            if (Swal.isVisible()) {
                Swal.close();
            }
            doLogout();
        }, WARNING_BEFORE * 1000);
    }

    function extendSession() {
        $.get(PING_URL)
            .done(function () {
                resetTimers();
            })
            .fail(function () {
                doLogout();
            });
    }

    function doLogout() {
        clearTimeout(warningTimer);
        clearTimeout(logoutTimer);
        clearInterval(countdownInterval);

        $.ajax({
            url: LOGOUT_URL,
            type: "POST",
            data: { _token: $('meta[name="csrf-token"]').attr("content") },
            complete: function () {
                window.location.href = LOGIN_URL;
            },
        });
    }

    // Aktivitas user (klik, keyboard, scroll) reset timer supaya session tidak
    // tiba-tiba warning padahal user masih aktif
    let activityDebounce;
    $(document).on("mousemove click keypress scroll", function () {
        clearTimeout(activityDebounce);
        activityDebounce = setTimeout(function () {
            if (!Swal.isVisible()) {
                resetTimers();
            }
        }, 1000);
    });

    resetTimers();
});
