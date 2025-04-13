// Bar Chart (Monthly Active Users)
const barCtx = document.getElementById("barChart").getContext("2d");
new Chart(barCtx, {
    type: "bar",
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug"],
        datasets: [
            {
                label: "New Users",
                data: [250, 300, 400, 450, 600, 700, 850, 900],
                backgroundColor: "#1E293B",
            },
            {
                label: "Returning Users",
                data: [150, 200, 250, 300, 350, 400, 450, 500],
                backgroundColor: "#8C7851",
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
            },
        },
    },
});

// Pie Chart (Book Category Distribution)
const pieCtx = document.getElementById("pieChart").getContext("2d");
new Chart(pieCtx, {
    type: "pie",
    data: {
        labels: ["Website", "Machine Learning", "Data Science", "Mobile"],
        datasets: [
            {
                data: [40, 25, 20, 15],
                backgroundColor: ["#0A0A0A", "#D4AF37", "#D9D9D9", "#D2B48C"],
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
    },
});
