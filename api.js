async function registerUser(name, email, password, confirmPassword) {
    const response = await fetch("http://localhost/afrin_cooking/api/auth.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "register",
            name,
            email,
            password,
            confirmPassword
        })
    });

    return await response.json();
}

module.exports = { registerUser };