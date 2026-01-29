    document.addEventListener('DOMContentLoaded', function() {
        let activeTimer = null;  // Track the active timer
        let elapsedTime = 0;     // Track elapsed time in seconds
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
            button.addEventListener('click', function() {
                const key = this.getAttribute('data-key');
                const project_type_name = this.getAttribute('data-project-type-name');
                const project_process_name = this.getAttribute('data-project-process-name');
                const startButton = document.querySelector('.start-timer[data-key="' + key + '"]');
                const stopButton = document.querySelector('.stop-timer[data-key="' + key + '"]');

                startButton.classList.add('d-none');
                stopButton.classList.remove('d-none');
                document.getElementById("time-remaining-" + key).classList.remove('d-none');

                // Reset elapsed time
                elapsedTime = 0;

                let hoursRemaining = parseFloat(document.getElementById('time-remaining-' + key).textContent);
                let timeRemaining = Math.floor(hoursRemaining * 3600); // Convert hours to seconds

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
                        alert('Time is up for this process.');
                        updateProcessStatus(key, project_type_name, project_process_name, elapsedTime);  // Save actual time
                        startButton.disabled = true;
                        stopButton.classList.add('d-none');  // Hide Stop Timer
                        enableNextButton(key);  // Enable the next timer button
                    }
                }, 1000);
            });

            // Add event listener for Stop Timer
            const stopButton = document.querySelector('.stop-timer[data-key="' + button.getAttribute('data-key') + '"]');
            if (stopButton) {
                stopButton.addEventListener('click', function() {
                    clearInterval(activeTimer);  // Stop the timer

                    // Calculate actual time in seconds
                    const actualTimeInSeconds = elapsedTime; // This is in seconds

                    // Convert to HH:MM:SS format
                    const hours = Math.floor(actualTimeInSeconds / 3600);
                    const minutes = Math.floor((actualTimeInSeconds % 3600) / 60);
                    const seconds = actualTimeInSeconds % 60;

                    // Format time to ensure two digits for each part
                    const formattedTime = [
                        hours.toString().padStart(2, '0'),
                        minutes.toString().padStart(2, '0'),
                        seconds.toString().padStart(2, '0')
                    ].join(':');

                    // Update actual time display
                    const actualTimeCell = document.querySelector('td:nth-child(6)'); // Adjust based on your actual time column index
                    actualTimeCell.textContent = formattedTime; // Update the cell with the new actual time

                    // Send data to the server in hours (for processing)
                    const key = stopButton.getAttribute('data-key');
                    const project_type_name = stopButton.getAttribute('data-project-type-name');
                    const project_process_name = stopButton.getAttribute('data-project-process-name');
                    updateProcessStatus(key, project_type_name, project_process_name, formattedTime); // Send formatted time to the server
                });
            }
        });

        function updateProcessStatus(key, project_type_name, project_process_name, actualTime) {
            console.log(key, project_type_name, project_process_name, actualTime);
            fetch(updateStatusUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    process_key: key,
                    status: 1,  // Update status to completed
                    actual_time: actualTime, // Convert elapsed time to hours
                    project_type_name: project_type_name,
                    project_process_name: project_process_name
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Process status and actual time updated successfully.');
                    location.reload();  // Reload to reflect status
                } else {
                    alert("Error in updating status.");
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });


    // sec = 60
    // min = 60
    // hours = 60 * 60 * 1 = 3600
    // 3600 * 0.001 = 3 sec

