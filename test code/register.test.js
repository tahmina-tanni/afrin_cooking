const { registerUser } = require('../api.js');

describe('RegisterTest', () => {

    beforeAll(() => {
        global.fetch = jest.fn(); // fetch mock
    });

    beforeEach(() => {
        jest.clearAllMocks(); // reset mock before each test
    });

    test('testRegister_Success', async () => {

        // Mock API response
        global.fetch.mockResolvedValue({
            json: async () => ({
                success: true,
                message: 'Registration successful'
            })
        });

        // Call function
        const result = await registerUser(
            'Sumaiya',
            'sumaiya@gmail.com',
            'pass123',
            'pass123'
        );

        // Check result
        expect(result.success).toBe(true);
        expect(result.message).toBe('Registration successful');
    });

});