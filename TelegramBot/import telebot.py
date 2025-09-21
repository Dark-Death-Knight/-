import telebot

bot = telebot.TeleBot('8038191080:AAEH1x4Jh1JQPKVjztrhGtOfw1btElAxlKA')



@bot.message_handler(commands=['start'])
def start(message):
    bot.send_message(message.chat.id, 'Привет')

bot.polling()