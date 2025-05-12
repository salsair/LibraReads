// Dummy user data
const users = [
    { id: 1, profile: "images/dashboard/jo.png", name: "Zayden Feroz", email: "zayden.feroz@email.com", role: "Admin" },
    { id: 2, profile: "images/dashboard/vita.jpg", name: "Ava Mirai", email: "ava.mirai@email.com", role: "Editor" },
    { id: 3, profile: "images/dashboard/vina.png", name: "Milo Renzo", email: "milo.renzo@email.com", role: "User" },
    { id: 4, profile: "images/dashboard/rifha.png", name: "Kaiya Zane", email: "kaiya.zane@email.com", role: "User" },
    { id: 5, profile: "images/dashboard/lena.png", name: "Ivy Raya", email: "ivy.raya@email.com", role: "User" },
    { id: 6, profile: "images/dashboard/der.jpg", name: "Jaxon Nova", email: "jaxon.nova@email.com", role: "User" },
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
