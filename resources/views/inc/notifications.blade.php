@can('access-orders')
    <audio id="notification-sound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>
    <script>
        let lastNotificationId = null;

        async function fetchNotifications() {
            try {
                const response = await fetch("{{ route('notifications.fetch') }}");
                const data = await response.json();
                if (data.length > 0 && data[0].id !== lastNotificationId) {
                    lastNotificationId = data[0].id;
                    const audio = document.getElementById('notification-sound');
                    audio.play();
                    Toastify({
                        text: `📦 ${data[0].title}\n📱 ${data[0].phone} | ${data[0].city}`,
                        duration: 7000,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                    }).showToast();
                }
            } catch (error) {
                console.error('Notification fetch error:', error);
            }
        }

        setInterval(fetchNotifications, 3000);
    </script>
@endcan
