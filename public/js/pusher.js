document.addEventListener('DOMContentLoaded', function() {
    const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
    const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

    if (!pusherKey || !pusherCluster) {
        console.error('Pusher environment variables are missing.');
        return;
    }

    // Initialize Pusher
    const pusher = new Pusher(pusherKey, {
        cluster: pusherCluster,
        encrypted: true,
    });

    console.log('Pusher initialized successfully:', pusher);

    // Initialize Pusher
    // const pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
    //     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    //     encrypted: true,
    // });

    // Subscribe to the channel
    const channel = pusher.subscribe('private-product.' + productId);

    // Listen for the event
    channel.bind('process.started', function(data) {
        console.log('Process Started: ', data);

        const productId = data.productId;
        const startTime = new Date(data.startTime).getTime();

        // Update UI or start the timer based on the event
        startFrontendTimer(productId, startTime);
    });

    function startFrontendTimer(productId, startTime) {
        const timerButton = document.querySelector(`.start-timer[data-product-id="${productId}"]`);
        if (timerButton) {
            timerButton.classList.add('d-none');
            const stopButton = document.querySelector(`.stop-timer[data-product-id="${productId}"]`);
            if (stopButton) stopButton.classList.remove('d-none');

            // Start the timer UI logic
            const timeRemainingDisplay = document.getElementById('time-remaining-' + productId);
            if (timeRemainingDisplay) {
                const elapsedTime = (Date.now() - startTime) / 1000; // Calculate elapsed time in seconds
                const timeRemaining = Math.max(timeRemaining - elapsedTime, 0); // Adjust the timer display
                updateTimerDisplay(timeRemainingDisplay, timeRemaining);
            }
        }
    }

    function updateTimerDisplay(displayElement, timeRemaining) {
        let hours = Math.floor(timeRemaining / 3600);
        let minutes = Math.floor((timeRemaining % 3600) / 60);
        let seconds = timeRemaining % 60;

        displayElement.textContent =
            `${hours.toString().padStart(2, '0')}:
             ${minutes.toString().padStart(2, '0')}:
             ${seconds.toString().padStart(2, '0')}`;
    }
});
