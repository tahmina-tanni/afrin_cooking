// tests/deleteRecipe.test.js

const { deleteRecipeById } = require('../api.js');

describe('DeleteRecipeTest', () => {

    beforeAll(() => {
        console.log('Before All - DeleteRecipeTest');
        global.fetch = jest.fn();
    });

    afterAll(() => {
        console.log('After All - DeleteRecipeTest');
    });

    beforeEach(() => {
        console.log('Before Each');
        jest.clearAllMocks();
    });

    afterEach(() => {
        console.log('After Each');
    });

    // @Test
    // assertEquals("Recipe deleted successfully", result.message)
    test('testDeleteRecipe_Success', async () => {
        global.fetch.mockResolvedValue({
            json: async () => ({ success: true, message: 'Recipe deleted successfully' })
        });

        const result = await deleteRecipeById(5);

        expect(result.success).toBe(true);
        expect(result.message).toBe('Recipe deleted successfully');
    });

});
