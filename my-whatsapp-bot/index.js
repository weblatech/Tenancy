const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');

const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: {
        headless: true,
        // یہاں ہم نے پاتھ کو ڈبل بیک سلیش کے ساتھ لکھا ہے
        executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe' 
    }
});

client.on('qr', (qr) => {
    qrcode.generate(qr, {small: true});
});

client.on('ready', () => {
    console.log('WhatsApp client is ready!');
});

client.initialize();