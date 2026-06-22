// tests/login.test.js

const { loginUser } = require('../api.js');

describe('LoginTest', () => {

    beforeAll(() => {
        console.log('Before All - LoginTest');
        global.fetch = jest.fn();
    });

    afterAll(() => {
        console.log('After All - LoginTest');
    });

    beforeEach(() => {
        console.log('Before Each');
        jest.clearAllMocks();
    });

    afterEach(() => {
        console.log('After Each');
    });

    // @Test
    // assertEquals("Login successful", result.message)
    test('testLogin_Success', async () => {
        global.fetch.mockResolvedValue({
            json: async () => ({ success: true, message: 'Login successful' })
        });

        const result = await loginUser('sumaiya@gmail.com', 'password123');

        expect(result.success).toBe(true);
        expect(result.message).toBe('Login successful');
    });

});
