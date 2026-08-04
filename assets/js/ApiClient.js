// assets/js/ApiClient.js - Singleton Pattern (Afrin's Cooking)
// DB connection er moto ekhane ekta single ApiClient instance shob jaygay reuse hobe

class ApiClient {
    constructor() {
        if (ApiClient.instance) {
            return ApiClient.instance;
        }
        this.baseUrl = "api";
        ApiClient.instance = this;
    }

    // GET request helper
    async get(endpoint, params = {}) {
        const query = new URLSearchParams(params).toString();
        const url = query ? `${this.baseUrl}/${endpoint}?${query}` : `${this.baseUrl}/${endpoint}`;
        const response = await fetch(url);
        return await response.json();
    }

    // POST (FormData) helper — file upload shoho
    async postForm(endpoint, formData) {
        const response = await fetch(`${this.baseUrl}/${endpoint}`, {
            method: "POST",
            body: formData
        });
        return await response.json();
    }

    // POST (JSON) helper
    async request(endpoint, body) {
        const response = await fetch(`${this.baseUrl}/${endpoint}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body)
        });
        return await response.json();
    }

    // ===== RECIPES =====
    async getRecipes(options = {}) {
        return await this.get("recipes.php", options);
    }

    async getRecipeDetail(id) {
        return await this.get("recipe_detail.php", { id });
    }

    async addRecipe(formData) {
        return await this.postForm("recipes.php", formData);
    }

    // ===== CATEGORIES =====
    async getCategories() {
        return await this.get("categories.php");
    }

    // ===== NEWSLETTER =====
    async subscribeNewsletter(email) {
        return await this.request("newsletter.php", { email });
    }

    // ===== AUTH =====
    async checkLoginStatus() {
        return await this.get("check_login.php");
    }

    async loginUser(email, password) {
        const formData = new FormData();
        formData.append("action", "login");
        formData.append("email", email);
        formData.append("password", password);
        return await this.postForm("auth.php", formData);
    }

    async registerUser(name, email, password, confirmPassword) {
        const formData = new FormData();
        formData.append("action", "register");
        formData.append("name", name);
        formData.append("email", email);
        formData.append("password", password);
        formData.append("confirm_password", confirmPassword);
        return await this.postForm("auth.php", formData);
    }

    async logoutUser() {
        const formData = new FormData();
        formData.append("action", "logout");
        return await this.postForm("auth.php", formData);
    }

    // ===== RECIPE ACTIONS =====
    async rateRecipe(recipeId, rating) {
        return await this.request("rate_recipe.php", { recipe_id: recipeId, rating });
    }

    async deleteRecipe(recipeId) {
        return await this.request("delete_recipe.php", { recipe_id: recipeId });
    }

    async featureRecipe(recipeId) {
        return await this.request("feature_recipe.php", { recipe_id: recipeId });
    }

    async unfeatureRecipe(recipeId) {
        return await this.request("unfeature_recipe.php", { recipe_id: recipeId });
    }
}

// Global single instance — app.js eta use korbe
const apiClient = new ApiClient();