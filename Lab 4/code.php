import html
import telebot
from telebot import types
import datetime

class User:
    """Класс для представления пользователя"""
    def __init__(self, user_id, first_name, last_name=""):
        self.user_id = user_id
        self.first_name = first_name
        self.last_name = last_name
        self.join_date = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        self.message_count = 0
    
    def increment_message_count(self):
        """Увеличивает счетчик сообщений"""
        self.message_count += 1
    
    def to_dict(self):
        """Возвращает данные пользователя в виде словаря"""
        return {
            'user_id': self.user_id,
            'first_name': self.first_name,
            'last_name': self.last_name,
            'join_date': self.join_date,
            'message_count': self.message_count
        }

class UserManager:
    """Менеджер для работы с пользователями"""
    def __init__(self):
        self.users = {}
    
    def get_or_create_user(self, user_id, first_name, last_name=""):
        """Получает или создает пользователя"""
        if user_id not in self.users:
            self.users[user_id] = User(user_id, first_name, last_name)
        return self.users[user_id]
    
    def get_user_stats(self, user_id):
        """Получает статистику пользователя"""
        user = self.users.get(user_id)
        return user.to_dict() if user else None

class MessageFormatter:
    """Класс для форматирования сообщений"""
    
    @staticmethod
    def welcome_message(first_name):
        """Форматирует приветственное сообщение"""
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
    
    @staticmethod
    def help_message():
        """Форматирует сообщение помощи"""
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
    
    @staticmethod
    def text_formatting_examples():
        """Примеры HTML форматирования"""
        return """
<b>🖋 Примеры HTML форматирования:</b>

<b>Жирный текст</b> - <code>&lt;b&gt;текст&lt;/b&gt;</code>
<em>Курсив</em> - <code>&lt;em&gt;текст&lt;/em&gt;</code>
<u>Подчеркнутый</u> - <code>&lt;u&gt;текст&lt;/u&gt;</code>
<s>Зачеркнутый</s> - <code>&lt;s&gt;текст&lt;/s&gt;</code>
<code>Моноширинный</code> - <code>&lt;code&gt;текст&lt;/code&gt;</code>
<a href="https://telegram.org">Ссылка</a> - <code>&lt;a href="URL"&gt;текст&lt;/a&gt;</code>
"""
    
    @staticmethod
    def stats_message(user_data):
        """Форматирует статистику пользователя"""
        if user_data:
            return f"""
<b>📊 Ваша статистика:</b>

👤 Имя: {user_data['first_name']}
📅 Дата регистрации: {user_data['join_date']}
📨 Сообщений отправлено: {user_data['message_count']}
🆔 Ваш ID: <code>{user_data['user_id']}</code>
"""
        return "❌ Данные не найдены. Используйте /start"
    
    @staticmethod
    def time_message():
        """Форматирует сообщение о времени"""
        return f"""
<b>🕐 Текущее время:</b>

📅 Дата: <code>{datetime.datetime.now().strftime('%d.%m.%Y')}</code>
⏰ Время: <code>{datetime.datetime.now().strftime('%H:%M:%S')}</code>
🌍 Часовой пояс: МСК (UTC+3)
"""
    
    @staticmethod
    def weather_message():
        """Форматирует сообщение о погоде"""
        return """
<b>🌤 Погода в Москве:</b>

🌡 Температура: +5°C
💨 Ветер: 3 м/с
💧 Влажность: 75%
☁️ Облачность: переменная
📋 Описание: Легкая облачность

<em>Обновлено: сейчас</em>
"""
    
    @staticmethod
    def random_message(number):
        """Форматирует сообщение со случайным числом"""
        return f"""
<b>🎲 Случайное число:</b>

Ваше число: <code>{number}</code>
Диапазон: 1-100

<em>Хотите еще? Используйте команду снова!</em>
"""
    
    @staticmethod
    def info_message():
        """Форматирует информационное сообщение"""
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
    
    @staticmethod
    def echo_message(text, first_name):
        """Форматирует эхо-ответ"""
        return f"""
<b>📨 Получено сообщение:</b>

<code>{html.escape(text)}</code>

📊 Длина: {len(text)} символов
👤 От: {first_name}
"""

class KeyboardManager:
    """Менеджер для работы с клавиатурами"""
    
    @staticmethod
    def create_reply_keyboard():
        """Создает reply-клавиатуру"""
        markup = types.ReplyKeyboardMarkup(resize_keyboard=True)
        buttons = [
            types.KeyboardButton('📊 Информация'),
            types.KeyboardButton('🆘 Помощь'),
            types.KeyboardButton('🌐 Сайт')
        ]
        markup.add(*buttons)
        return markup
    
    @staticmethod
    def create_inline_keyboard():
        """Создает inline-клавиатуру"""
        markup = types.InlineKeyboardMarkup()
        buttons = [
            types.InlineKeyboardButton('📊 Статистика', callback_data='stats'),
            types.InlineKeyboardButton('🕐 Время', callback_data='time'),
            types.InlineKeyboardButton('🎲 Случайное число', callback_data='random'),
            types.InlineKeyboardButton('🌐 Сайт', url='https://telegram.org')
        ]
        markup.row(buttons[0], buttons[1])
        markup.row(buttons[2], buttons[3])
        return markup
    
    @staticmethod
    def create_website_keyboard():
        """Создает клавиатуру для сайта"""
        markup = types.InlineKeyboardMarkup()
        button = types.InlineKeyboardButton('Перейти на сайт Telegram', url='https://telegram.org')
        markup.add(button)
        return markup

class CommandHandler:
    """Обработчик команд"""
    
    def __init__(self, bot, user_manager, formatter, keyboard_manager):
        self.bot = bot
        self.user_manager = user_manager
        self.formatter = formatter
        self.keyboard_manager = keyboard_manager
        self.setup_handlers()
    
    def setup_handlers(self):
        """Настраивает обработчики команд"""
        @self.bot.message_handler(commands=['start'])
        def start(message):
            self.handle_start(message)
        
        @self.bot.message_handler(commands=['help'])
        def help_command(message):
            self.handle_help(message)
        
        @self.bot.message_handler(commands=['text'])
        def text_formatting(message):
            self.handle_text_formatting(message)
        
        @self.bot.message_handler(commands=['stats'])
        def stats(message):
            self.handle_stats(message)
        
        @self.bot.message_handler(commands=['time'])
        def current_time(message):
            self.handle_time(message)
        
        @self.bot.message_handler(commands=['weather'])
        def weather(message):
            self.handle_weather(message)
        
        @self.bot.message_handler(commands=['random'])
        def random_number(message):
            self.handle_random(message)
        
        @self.bot.message_handler(commands=['inline'])
        def inline_keyboard(message):
            self.handle_inline(message)
    
    def handle_start(self, message):
        """Обрабатывает команду /start"""
        user = self.user_manager.get_or_create_user(
            message.from_user.id,
            message.from_user.first_name,
            message.from_user.last_name
        )
        
        markup = self.keyboard_manager.create_reply_keyboard()
        welcome_text = self.formatter.welcome_message(message.from_user.first_name)
        
        self.bot.send_message(message.chat.id, welcome_text, 
                            reply_markup=markup, parse_mode='HTML')
    
    def handle_help(self, message):
        """Обрабатывает команду /help"""
        user = self.user_manager.get_or_create_user(
            message.from_user.id,
            message.from_user.first_name
        )
        user.increment_message_count()
        
        help_text = self.formatter.help_message()
        self.bot.send_message(message.chat.id, help_text, parse_mode='HTML')
    
    def handle_text_formatting(self, message):
        """Обрабатывает команду /text"""
        user = self.user_manager.get_or_create_user(
            message.from_user.id,
            message.from_user.first_name
        )
        user.increment_message_count()
        
        formatting_text = self.formatter.text_formatting_examples()
        self.bot.send_message(message.chat.id, formatting_text, parse_mode='HTML')
    
    def handle_stats(self, message):
        """Обрабатывает команду /stats"""
        user = self.user_manager.get_or_create_user(
            message.from_user.id,
            message.from_user.first_name
        )
        user.increment_message_count()
        
        user_data = self.user_manager.get_user_stats(message.from_user.id)
        stats_text = self.formatter.stats_message(user_data)
        self.bot.send_message(message.chat.id, stats_text, parse_mode='HTML')
    
    def handle_time(self, message):
        """Обрабатывает команду /time"""
        user = self.user_manager.get_or_create_user(
            message.from_user.id,
            message.from_user.first_name
        )
        user.increment_message_count()
        
        time_text = self.formatter.time_message()
        self.bot.send_message(message.chat.id, time_text, parse_mode='HTML')
    
    def handle_weather(self, message):
        """Обрабатывает команду /weather"""
        user = self.user_manager.get_or_create_user(
            message.from_user.id,
            message.from_user.first_name
        )
        user.increment_message_count()
        
        try:
            weather_text = self.formatter.weather_message()
            self.bot.send_message(message.chat.id, weather_text, parse_mode='HTML')
        except Exception:
            self.bot.send_message(message.chat.id, "❌ Ошибка получения погоды")
    
    def handle_random(self, message):
        """Обрабатывает команду /random"""
        user = self.user_manager.get_or_create_user(
            message.from_user.id,
            message.from_user.first_name
        )
        user.increment_message_count()
        
        import random
        number = random.randint(1, 100)
        random_text = self.formatter.random_message(number)
        self.bot.send_message(message.chat.id, random_text, parse_mode='HTML')
    
    def handle_inline(self, message):
        """Обрабатывает команду /inline"""
        user = self.user_manager.get_or_create_user(
            message.from_user.id,
            message.from_user.first_name
        )
        user.increment_message_count()
        
        markup = self.keyboard_manager.create_inline_keyboard()
        self.bot.send_message(message.chat.id, "🔘 Выберите действие:", reply_markup=markup)

class MessageHandler:
    """Обработчик сообщений"""
    
    def __init__(self, bot, user_manager, formatter, keyboard_manager, command_handler):
        self.bot = bot
        self.user_manager = user_manager
        self.formatter = formatter
        self.keyboard_manager = keyboard_manager
        self.command_handler = command_handler
        self.setup_handlers()
    
    def setup_handlers(self):
        """Настраивает обработчики сообщений"""
        @self.bot.message_handler(content_types=['text'])
        def handle_text(message):
            self.handle_text_message(message)
        
        @self.bot.message_handler(content_types=['photo', 'document', 'sticker'])
        def handle_media(message):
            self.handle_media_message(message)
    
    def handle_text_message(self, message):
        """Обрабатывает текстовые сообщения"""
        user = self.user_manager.get_or_create_user(
            message.from_user.id,
            message.from_user.first_name,
            message.from_user.last_name
        )
        user.increment_message_count()
        
        if message.text == '📊 Информация':
            info_text = self.formatter.info_message()
            self.bot.send_message(message.chat.id, info_text, parse_mode='HTML')
        
        elif message.text == '🆘 Помощь':
            self.command_handler.handle_help(message)
        
        elif message.text == '🌐 Сайт':
            markup = self.keyboard_manager.create_website_keyboard()
            self.bot.send_message(message.chat.id, "Нажмите кнопку для перехода:", reply_markup=markup)
        
        else:
            response = self.formatter.echo_message(message.text, message.from_user.first_name)
            self.bot.send_message(message.chat.id, response, parse_mode='HTML')
    
    def handle_media_message(self, message):
        """Обрабатывает медиа-сообщения"""
        user = self.user_manager.get_or_create_user(
            message.from_user.id,
            message.from_user.first_name
        )
        user.increment_message_count()
        
        self.bot.send_message(message.chat.id, f"📎 Получен медиа-файл! Тип: {message.content_type}")

class CallbackHandler:
    """Обработчик callback запросов"""
    
    def __init__(self, bot, user_manager):
        self.bot = bot
        self.user_manager = user_manager
        self.setup_handler()
    
    def setup_handler(self):
        """Настраивает обработчик callback'ов"""
        @self.bot.callback_query_handler(func=lambda call: True)
        def callback_handler(call):
            self.handle_callback(call)
    
    def handle_callback(self, call):
        """Обрабатывает callback запросы"""
        if call.data == 'stats':
            self._handle_stats_callback(call)
        elif call.data == 'time':
            self._handle_time_callback(call)
        elif call.data == 'random':
            self._handle_random_callback(call)
    
    def _handle_stats_callback(self, call):
        """Обрабатывает callback статистики"""
        user_data = self.user_manager.get_user_stats(call.from_user.id)
        if user_data:
            count = user_data['message_count']
            self.bot.answer_callback_query(call.id, f"Вы отправили {count} сообщений")
        else:
            self.bot.answer_callback_query(call.id, "Данные не найдены")
    
    def _handle_time_callback(self, call):
        """Обрабатывает callback времени"""
        current_time = datetime.datetime.now().strftime("%H:%M:%S")
        self.bot.answer_callback_query(call.id, f"Текущее время: {current_time}")
    
    def _handle_random_callback(self, call):
        """Обрабатывает callback случайного числа"""
        import random
        number = random.randint(1, 100)
        self.bot.answer_callback_query(call.id, f"Случайное число: {number}")

class TelegramBot:
    """Основной класс Telegram бота"""
    
    def __init__(self, token):
        self.bot = telebot.TeleBot(token)
        self.user_manager = UserManager()
        self.formatter = MessageFormatter()
        self.keyboard_manager = KeyboardManager()
        
        # Инициализация обработчиков
        self.command_handler = CommandHandler(
            self.bot, self.user_manager, self.formatter, self.keyboard_manager
        )
        
        self.callback_handler = CallbackHandler(self.bot, self.user_manager)
        
        self.message_handler = MessageHandler(
            self.bot, self.user_manager, self.formatter, 
            self.keyboard_manager, self.command_handler
        )
    
    def run(self):
        """Запускает бота"""
        print("Бот запущен...")
        try:
            self.bot.polling(none_stop=True)
        except Exception as e:
            print(f"Ошибка: {e}")

# Запуск бота
if __name__ == "__main__":
    bot = TelegramBot('8038191080:AAEH1x4Jh1JQPKVjztrhGtOfw1btElAxlKA')
    bot.run()
