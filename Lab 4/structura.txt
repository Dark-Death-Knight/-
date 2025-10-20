import html
import telebot
from telebot import types
import requests
import json
import datetime

bot = telebot.TeleBot('8038191080:AAEH1x4Jh1JQPKVjztrhGtOfw1btElAxlKA')

# Хранилище для данных пользователей
user_data = {}

# Класс для управления пользователями
class UserManager:
    def __init__(self):
        self.user_data = {}
    
    def get_or_create_user(self, user_id, first_name, last_name=""):
        if user_id not in self.user_data:
            self.user_data[user_id] = {
                'first_name': first_name,
                'last_name': last_name,
                'join_date': datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                'message_count': 0
            }
        return self.user_data[user_id]
    
    def increment_message_count(self, user_id):
        if user_id in self.user_data:
            self.user_data[user_id]['message_count'] += 1
    
    def get_user_stats(self, user_id):
        return self.user_data.get(user_id)

# Класс для создания клавиатур
class KeyboardFactory:
    def create_main_reply_keyboard(self):
        markup = types.ReplyKeyboardMarkup(resize_keyboard=True)
        item1 = types.KeyboardButton('📊 Информация')
        item2 = types.KeyboardButton('🆘 Помощь')
        item3 = types.KeyboardButton('🌐 Сайт')
        markup.add(item1, item2, item3)
        return markup
    
    def create_inline_keyboard(self):
        markup = types.InlineKeyboardMarkup()
        btn1 = types.InlineKeyboardButton('📊 Статистика', callback_data='stats')
        btn2 = types.InlineKeyboardButton('🕐 Время', callback_data='time')
        btn3 = types.InlineKeyboardButton('🎲 Случайное число', callback_data='random')
        btn4 = types.InlineKeyboardButton('🌐 Сайт', url='https://telegram.org')
        markup.add(btn1, btn2)
        markup.add(btn3, btn4)
        return markup
    
    def create_website_keyboard(self):
        markup = types.InlineKeyboardMarkup()
        btn = types.InlineKeyboardButton('Перейти на сайт Telegram', url='https://telegram.org')
        markup.add(btn)
        return markup

# Класс для форматирования сообщений
class MessageFormatter:
    def format_welcome_message(self, first_name):
        return f"""
    👋 Привет, {first_name}!

Я многофункциональный бот с различными возможностями:

📝 /text - Форматирование текста
📊 /stats - Статистика
🕐 /time - Текущее время
🌤 /weather - Погода
🎲 /random - Случайное число

Выберите команду или используйте кнопки ниже!
    """
    
    def format_help_message(self):
        return """
<b>📋 Доступные команды:</b>

<code>/start</code> - Запуск бота
<code>/help</code> - Справка по командам
<code>/text</code> - HTML форматирование
<code>/stats</code> - Ваша статистика
<code>/time</code> - Текущее время
<code>/weather</code> - Погода в Москве
<code>/random</code> - Случайное число
<code>/inline</code> - Inline клавиатура

<em>Также можете просто написать сообщение - я его обработаю!</em>
    """
    
    def format_text_examples(self):
        return """
<b>🖋 Примеры HTML форматирования:</b>

<b>Жирный текст</b> - <code>&lt;b&gt;текст&lt;/b&gt;</code>
<em>Курсив</em> - <code>&lt;em&gt;текст&lt;/em&gt;</code>
<u>Подчеркнутый</u> - <code>&lt;u&gt;текст&lt;/u&gt;</code>
<s>Зачеркнутый</s> - <code>&lt;s&gt;текст&lt;/s&gt;</code>
<code>Моноширинный</code> - <code>&lt;code&gt;текст&lt;/code&gt;</code>
<a href="https://telegram.org">Ссылка</a> - <code>&lt;a href="URL"&gt;текст&lt;/a&gt;</code>
    """
    
    def format_stats_message(self, user_info, user_id):
        if user_info:
            return f"""
<b>📊 Ваша статистика:</b>

👤 Имя: {user_info['first_name']}
📅 Дата регистрации: {user_info['join_date']}
📨 Сообщений отправлено: {user_info['message_count']}
🆔 Ваш ID: <code>{user_id}</code>
        """
        else:
            return "❌ Данные не найдены. Используйте /start"
    
    def format_time_message(self):
        current_time = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        return f"""
<b>🕐 Текущее время:</b>

📅 Дата: <code>{datetime.datetime.now().strftime('%d.%m.%Y')}</code>
⏰ Время: <code>{datetime.datetime.now().strftime('%H:%M:%S')}</code>
🌍 Часовой пояс: МСК (UTC+3)
    """
    
    def format_weather_message(self):
        return """
<b>🌤 Погода в Москве:</b>

🌡 Температура: +5°C
💨 Ветер: 3 м/с
💧 Влажность: 75%
☁️ Облачность: переменная
📋 Описание: Легкая облачность

<em>Обновлено: сейчас</em>
        """
    
    def format_random_message(self, number):
        return f"""
<b>🎲 Случайное число:</b>

Ваше число: <code>{number}</code>
Диапазон: 1-100

<em>Хотите еще? Используйте команду снова!</em>
    """
    
    def format_info_message(self):
        return """
<b>ℹ️ Информация о боте:</b>

Это демонстрационный Telegram бот
с различными функциями и командами.

Разработан на Python с использованием
библиотеки pyTelegramBotAPI.

🔧 Функции:
• HTML форматирование
• Статистика пользователей
• Inline клавиатуры
• Обработка callback'ов
        """
    
    def format_echo_message(self, message_text, first_name):
        return f"""
<b>📨 Получено сообщение:</b>

<code>{html.escape(message_text)}</code>

📊 Длина: {len(message_text)} символов
👤 От: {first_name}
        """

# Класс для обработки callback запросов
class CallbackHandler:
    def __init__(self, user_manager):
        self.user_manager = user_manager
    
    def handle_callback(self, call, bot):
        if call.data == 'stats':
            self._handle_stats_callback(call, bot)
        elif call.data == 'time':
            self._handle_time_callback(call, bot)
        elif call.data == 'random':
            self._handle_random_callback(call, bot)
    
    def _handle_stats_callback(self, call, bot):
        user_id = call.from_user.id
        user_info = self.user_manager.get_user_stats(user_id)
        if user_info:
            count = user_info['message_count']
            bot.answer_callback_query(call.id, f"Вы отправили {count} сообщений")
        else:
            bot.answer_callback_query(call.id, "Данные не найдены")
    
    def _handle_time_callback(self, call, bot):
        current_time = datetime.datetime.now().strftime("%H:%M:%S")
        bot.answer_callback_query(call.id, f"Текущее время: {current_time}")
    
    def _handle_random_callback(self, call, bot):
        import random
        number = random.randint(1, 100)
        bot.answer_callback_query(call.id, f"Случайное число: {number}")

# Создаем экземпляры классов
user_manager = UserManager()
keyboard_factory = KeyboardFactory()
message_formatter = MessageFormatter()
callback_handler = CallbackHandler(user_manager)

# Обработчики команд
@bot.message_handler(commands=['start'])
def start(message):
    user_info = user_manager.get_or_create_user(
        message.from_user.id,
        message.from_user.first_name,
        message.from_user.last_name
    )
    
    markup = keyboard_factory.create_main_reply_keyboard()
    welcome_text = message_formatter.format_welcome_message(message.from_user.first_name)
    
    bot.send_message(message.chat.id, welcome_text, reply_markup=markup, parse_mode='HTML')

@bot.message_handler(commands=['help'])
def help_command(message):
    user_manager.increment_message_count(message.from_user.id)
    help_text = message_formatter.format_help_message()
    bot.send_message(message.chat.id, help_text, parse_mode='HTML')

@bot.message_handler(commands=['text'])
def text_formatting(message):
    user_manager.increment_message_count(message.from_user.id)
    formatting_examples = message_formatter.format_text_examples()
    bot.send_message(message.chat.id, formatting_examples, parse_mode='HTML')

@bot.message_handler(commands=['stats'])
def stats(message):
    user_manager.increment_message_count(message.from_user.id)
    user_info = user_manager.get_user_stats(message.from_user.id)
    stats_text = message_formatter.format_stats_message(user_info, message.from_user.id)
    bot.send_message(message.chat.id, stats_text, parse_mode='HTML')

@bot.message_handler(commands=['time'])
def current_time(message):
    user_manager.increment_message_count(message.from_user.id)
    time_text = message_formatter.format_time_message()
    bot.send_message(message.chat.id, time_text, parse_mode='HTML')

@bot.message_handler(commands=['weather'])
def weather(message):
    user_manager.increment_message_count(message.from_user.id)
    try:
        weather_text = message_formatter.format_weather_message()
        bot.send_message(message.chat.id, weather_text, parse_mode='HTML')
    except Exception as e:
        bot.send_message(message.chat.id, "❌ Ошибка получения погоды")

@bot.message_handler(commands=['random'])
def random_number(message):
    user_manager.increment_message_count(message.from_user.id)
    import random
    number = random.randint(1, 100)
    random_text = message_formatter.format_random_message(number)
    bot.send_message(message.chat.id, random_text, parse_mode='HTML')

@bot.message_handler(commands=['inline'])
def inline_keyboard(message):
    user_manager.increment_message_count(message.from_user.id)
    markup = keyboard_factory.create_inline_keyboard()
    bot.send_message(message.chat.id, "🔘 Выберите действие:", reply_markup=markup)

# Обработчик callback запросов
@bot.callback_query_handler(func=lambda call: True)
def callback_handler(call):
    callback_handler.handle_callback(call, bot)

# Обработчик текстовых сообщений
@bot.message_handler(content_types=['text'])
def handle_text(message):
    user_id = message.from_user.id
    user_manager.increment_message_count(user_id)
    
    # Обработка текста из кнопок
    if message.text == '📊 Информация':
        info_text = message_formatter.format_info_message()
        bot.send_message(message.chat.id, info_text, parse_mode='HTML')
    
    elif message.text == '🆘 Помощь':
        help_command(message)
    
    elif message.text == '🌐 Сайт':
        markup = keyboard_factory.create_website_keyboard()
        bot.send_message(message.chat.id, "Нажмите кнопку для перехода:", reply_markup=markup)
    
    else:
        # Эхо-ответ с обработкой HTML
        response = message_formatter.format_echo_message(message.text, message.from_user.first_name)
        bot.send_message(message.chat.id, response, parse_mode='HTML')

# Обработчик других типов контента
@bot.message_handler(content_types=['photo', 'document', 'sticker'])
def handle_media(message):
    user_manager.increment_message_count(message.from_user.id)
    bot.send_message(message.chat.id, f"📎 Получен медиа-файл! Тип: {message.content_type}")

# Запуск бота
if __name__ == "__main__":
    print("Бот запущен...")
    try:
        bot.polling(none_stop=True)
    except Exception as e:
        print(f"Ошибка: {e}")

