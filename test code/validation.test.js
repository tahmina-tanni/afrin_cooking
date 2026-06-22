const { addRecipe } = require('../api.js');

describe('ValidationTest', () => {

    beforeAll(() => {
        global.fetch = jest.fn();
    });

    beforeEach(() => {
        jest.clearAllMocks();
    });

    //  Empty ingredients
    test('testAddRecipe_EmptyIngredients_Fails', async () => {

        const result = await addRecipe(
            'Chicken Biryani',
            'Dinner',
            'Delicious food',
            [],   //  empty ingredients
            []
        );

        expect(fetch).not.toHaveBeenCalled();
        expect(result.success).toBe(false);
        expect(result.message).toBe('At least one ingredient is required');
    });

    //if Valid input API call 
    test('testAddRecipe_ValidInput_CallsAPI', async () => {

        // fake API response
        fetch.mockResolvedValue({
            json: async () => ({
                success: true,
                message: 'Recipe added'
            })
        });

        const result = await addRecipe(
            'Chicken Biryani',
            'Dinner',
            'Delicious food',
            ['Rice', 'Chicken'],   //  valid
            ['Cook', 'Serve']
        );

        expect(fetch).toHaveBeenCalled();
        expect(result.success).toBe(true);
    });

});