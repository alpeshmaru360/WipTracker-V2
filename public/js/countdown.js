document.addEventListener('DOMContentLoaded', function() {
    let activeTimer = null;
    let elapsedTime = 0;
    const timerButtons = document.querySelectorAll('.start-timer');

    // Function to enable the next button
    function enableNextButton(currentKey) {
        const nextKey = parseInt(currentKey) + 1;
        const nextStartButton = document.querySelector('.start-timer[data-key="' + nextKey + '"]');
        if (nextStartButton) {
            nextStartButton.classList.remove('disabled-btn'); // Enable the next button
            nextStartButton.disabled = false;
        }
    }

    timerButtons.forEach(function(button) {
        const key = button.getAttribute('data-key');
        const storedStartTime = localStorage.getItem('timerStartTime-' + key);
        const stopButton = document.querySelector('.stop-timer[data-key="' + key + '"]');
        const timeRemainingDisplay = document.getElementById('time-remaining-' + key);

        if (storedStartTime) {
            const timeRemaining = restoreTimer(key, storedStartTime);
            if (timeRemaining > 0) {
                // Restore timer display and stop button visibility
                timeRemainingDisplay.classList.remove('d-none'); // Show timer
                button.classList.add('d-none');  // Hide start button
                stopButton.classList.remove('d-none'); // Show stop button
                startTimer(timeRemaining, key);  // Start/Continue the timer
            } else {
                // If time has already expired, remove the timerStartTime from localStorage
                localStorage.removeItem('timerStartTime-' + key);
                timeRemainingDisplay.textContent = '00:00:00'; // Set to zero if time is up
            }
        }
    });

    // Start Timer event listener
    timerButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const key = this.getAttribute('data-key');
            const project_id = this.getAttribute('data-project-id');
            const product_id = this.getAttribute('data-product-id');
            const project_type_name = this.getAttribute('data-project-type-name');
            const project_process_name = this.getAttribute('data-project-process-name');
            const startButton = document.querySelector('.start-timer[data-key="' + key + '"]');
            const stopButton = document.querySelector('.stop-timer[data-key="' + key + '"]');
            const timeRemainingDisplay = document.getElementById('time-remaining-' + key);

            const storedStartTime = localStorage.getItem('timerStartTime-' + key);

            const seqQty = this.getAttribute('data-seq-qty');

            // Only start the timer if it hasn't started already
            if (!storedStartTime) {
                startButton.classList.add('d-none');
                stopButton.classList.remove('d-none');
                timeRemainingDisplay.classList.remove('d-none'); // Show the time

                // Reset elapsed time
                elapsedTime = 0;

                let hoursRemaining = parseFloat(timeRemainingDisplay.textContent);
                let timeRemaining = Math.floor(hoursRemaining * 3600); // Convert hours to seconds

                // Store start time in localStorage
                const startTime = Date.now();
                localStorage.setItem('timerStartTime-' + key, startTime);

                startTimer(timeRemaining,key,project_id,project_type_name,project_process_name,seqQty,product_id); // Start the timer
            }
        });
    });

    // Stop Timer event listener
    timerButtons.forEach(function(button) {
        const stopButton = document.querySelector('.stop-timer[data-key="' + button.getAttribute('data-key') + '"]');
        if (stopButton) {
            stopButton.addEventListener('click', function() {
                clearInterval(activeTimer);  // Stop the timer

                // Clear the start time from localStorage
                const key = stopButton.getAttribute('data-key');
                localStorage.removeItem('timerStartTime-' + key);

                // Update process status and actual time
                 const project_id = this.getAttribute('data-project-id');
                 const product_id = this.getAttribute('data-product-id');
                const project_type_name = stopButton.getAttribute('data-project-type-name');
                const project_process_name = stopButton.getAttribute('data-project-process-name');
                const seqQty = this.getAttribute('data-seq-qty');

                updateProcessStatus(key, project_id,project_type_name, project_process_name, elapsedTime,seqQty,product_id);

                // Hide stop button and show start button
                stopButton.classList.add('d-none');
                document.querySelector('.start-timer[data-key="' + key + '"]').classList.remove('d-none');
            });
        }
    });

    // Restore timer function
    function restoreTimer(key, storedStartTime) {
        const currentTime = Date.now();
        const elapsedTime = Math.floor((currentTime - storedStartTime) / 1000); // Convert milliseconds to seconds
        let hoursRemaining = parseFloat(document.getElementById('time-remaining-' + key).textContent);
        let timeRemaining = Math.floor(hoursRemaining * 3600) - elapsedTime;

        if (timeRemaining > 0) {
            return timeRemaining;
        } else {
            return 0;  // Timer has expired
        }
    }

    // Start Timer function
    function startTimer(timeRemaining, key,project_id,project_type_name,project_process_name,seqQty,product_id) {
        if (activeTimer) clearInterval(activeTimer);
        activeTimer = setInterval(function() {
            elapsedTime++; // Increment elapsed time every second
            timeRemaining--;

            let hours = Math.floor(timeRemaining / 3600);
            let minutes = Math.floor((timeRemaining % 3600) / 60);
            let seconds = timeRemaining % 60;
            let formattedTime = hours.toString().padStart(2, '0') + ':' +
                                minutes.toString().padStart(2, '0') + ':' +
                                seconds.toString().padStart(2, '0');
            document.getElementById('time-remaining-' + key).textContent = formattedTime;
            if (timeRemaining <= 0) {
                clearInterval(activeTimer);  // Stop the timer
                // alert('Time is up for this process.');
                enableNextButton(key);  // Enable the next timer button
                updateProcessStatus(key,project_id, project_type_name, project_process_name, elapsedTime,seqQty,product_id);  // Save actual time
            }
        }, 1000);
    }

    // Update process status function
    function updateProcessStatus(key, project_id,project_type_name, project_process_name, actualTime,seqQty,product_id) {
        console.log(actualTime);
        fetch(updateStatusUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                process_key: key,
                status: 1,
                actual_time: actualTime, 
                project_id: project_id,
                product_id: product_id,
                project_type_name: project_type_name,
                project_process_name: project_process_name,
                seqQty:seqQty
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Process status and actual time updated successfully.');
                location.reload();
            } else {
                alert("Error in updating status.");
            }
        })
        .catch(error => console.error('Error:', error));
    }

});


