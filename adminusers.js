// Dummy user data
const users = [
    { id: 1, profile: "images/dashboard/jo.png", name: "Jovana Semarang", email: "jopintul@email.com", role: "Admin" },
    { id: 2, profile: "images/dashboard/vita.jpg", name: "Vita Surabaya", email: "puff@email.com", role: "Editor" },
    { id: 3, profile: "images/dashboard/vina.png", name: "Vina Magetan", email: "vunk@email.com", role: "User" },
    { id: 4, profile: "images/dashboard/rifha.png", name: "Rifha Kenjeran", email: "rip@email.com", role: "User" },
    { id: 5, profile: "images/dashboard/lena.png", name: "Lena Jakarta", email: "len@email.com", role: "User" },
    { id: 6, profile: "images/dashboard/der.jpg", name: "Der Surabaya", email: "derz@email.com", role: "User" },
];

// Function to render users in table
function loadUsers() {
    const usersList = document.getElementById("usersList");
    usersList.innerHTML = "";

    users.forEach(user => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${user.id}</td>
            <td><img src="${user.profile}" alt="${user.name}"></td>
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td>${user.role}</td>
            <td class="actions">
                <button class="edit-btn">Edit</button>
                <button class="delete-btn">Delete</button>
            </td>
        `;
        usersList.appendChild(row);
    });
}

// Load users on page load
document.addEventListener("DOMContentLoaded", loadUsers);
