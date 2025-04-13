// User Growth Chart
const userGrowthCtx = document.getElementById("userGrowthChart").getContext("2d");
new Chart(userGrowthCtx, {
    type: "line",
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug"],
        datasets: [
            {
                label: "New Users",
                data: [120, 150, 180, 200, 250, 300, 350, 400],
                borderColor: "#0984e3",
                backgroundColor: "rgba(9, 132, 227, 0.2)",
                fill: true,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
            },
        },
    },
});

// Book Category Chart
const bookCategoryCtx = document.getElementById("bookCategoryChart").getContext("2d");
new Chart(bookCategoryCtx, {
    type: "doughnut",
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
        maintainAspectRatio: false,
    },
});
