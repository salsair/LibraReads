document.addEventListener("DOMContentLoaded", () => {
    const settingsForm = document.getElementById("settings-form");
    const notificationsToggle = document.getElementById("notifications");
    const autoUpdatesToggle = document.getElementById("auto-updates");

    // Simpan perubahan pada form
    settingsForm.addEventListener("submit", (event) => {
        event.preventDefault();
        alert("Settings saved successfully!");
    });

    // Toggle Notifications
    notificationsToggle.addEventListener("change", () => {
        if (notificationsToggle.checked) {
            alert("Notifications Enabled");
        } else {
            alert("Notifications Disabled");
        }
    });

    // Toggle Auto Updates
    autoUpdatesToggle.addEventListener("change", () => {
        if (autoUpdatesToggle.checked) {
            alert("Auto Updates Enabled");
        } else {
            alert("Auto Updates Disabled");
        }
    });
});
