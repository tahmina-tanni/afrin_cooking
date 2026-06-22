// tests/addRecipe.test.js

const { addRecipe } = require('../api.js');

describe('AddRecipeTest', () => {

    beforeAll(() => {
        console.log('Before All - AddRecipeTest');
        global.fetch = jest.fn();
    });

    afterAll(() => {
        console.log('After All - AddRecipeTest');
    });

    beforeEach(() => {
        console.log('Before Each');
        jest.clearAllMocks();
    });

    afterEach(() => {
        console.log('After Each');
    });

    // @Test
    // assertTrue(result.success)
    test('testAddRecipe_Success', async () => {
        global.fetch.mockResolvedValue({
            json: async () => ({ success: true, message: 'Recipe added successfully' })
        });

        const result = await addRecipe(
            'Chicken Biryani',
            'Dinner',
            'Delicious Bengali biryani',
            [{ amount: '500g', name: 'Chicken' }],
            ['Marinate chicken', 'Cook rice', 'Layer and dum']
        );

        expect(result.success).toBe(true);
    });

});
