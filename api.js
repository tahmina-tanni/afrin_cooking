// api.js


// REGISTER VALIDATION

function validateRegister(name, email, password, confirmPassword) {
    if (!name || !email || !password || !confirmPassword) {
        return {
            valid: false,
            message: "All fields are required"
        };
    }

    if (password !== confirmPassword) {
        return {
            valid: false,
            message: "Passwords do not match"
        };
    }

    return { valid: true };
}



// REGISTER USER FUNCTION

async function registerUser(name, email, password, confirmPassword) {

    const check = validateRegister(name, email, password, confirmPassword);

    if (!check.valid) {
        return {
            success: false,
            message: check.message
        };
    }

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



// Add RECIPE FUNCTION

async function addRecipe(name, category, description, ingredients, steps) {

    if (!ingredients || ingredients.length === 0) {
        return {
            success: false,
            message: "At least one ingredient is required"
        };
    }

    if (!steps || steps.length === 0) {
        return {
            success: false,
            message: "At least one step is required"
        };
    }

    const response = await fetch("http://localhost/afrin_cooking/api/recipe.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            name,
            category,
            description,
            ingredients,
            steps
        })
    });

    return await response.json();
}

// LOGIN USER FUNCTION

async function loginUser(email, password) {

    const response = await fetch("http://localhost/afrin_cooking/api/auth.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            action: "login",
            email,
            password
        })
    });

    return await response.json();
}


// DELETE RECIPE FUNCTION

async function deleteRecipeById(recipeId) {

    const response = await fetch("http://localhost/afrin_cooking/api/delete_recipe.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            recipe_id: recipeId
        })
    });

    return await response.json();
}

// =========================
// EXPORT FUNCTIONS
// =========================
module.exports = {
    registerUser,
    addRecipe,
    loginUser,
    deleteRecipeById
};