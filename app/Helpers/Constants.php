<?php

const BOT_CONVERSATION_STATE = 'bot_conv_chat_';

const FORMAT_DATE_TIME = 'Y/m/d - H:i:s';
const FORMAT_DATE = 'Y/m/d';
const REGEX_MOBILE = "/^09[0-9]{9}$/";
const MOBILE_LENGTH = 11;
const AUTH_CODE_FAKE = true;
const AUTH_CODE_LENGTH = 4;
const AUTH_CODE_EXPIRE_TIME = 90;

const MODELS_CREATED_AT_DEFAULT_FORMAT = 'Y-m-d H:i:s';
const MODELS_UPDATED_AT_DEFAULT_FORMAT = 'Y-m-d H:i:s';
const MODELS_CREATED_AT_FORMAT = 'Y-m-d H:i:s';
const MODELS_UPDATED_AT_FORMAT = 'Y-m-d H:i:s';

const STORE_USERNAME_LETTER_LENGTH = 3;
const STORE_USERNAME_NUMBER_LENGTH = 3;
const STORE_EXPIRE_DATE = 60;
const TEXT_LENGTH_DEFAULT = 100;
const TEXT_LENGTH_ADDRESS_DEFAULT = 200;
const TEXT_LENGTH_CATEGORY_DEFAULT = 20;

const STORE_DET_KEY_NAME = 'name';
const STORE_DET_KEY_DESCRIPTION = 'description';
const STORE_DET_KEY_CONTACT1 = 'contact1';
const STORE_DET_KEY_ADDRESS = 'address';
const STORE_DET_KEY_CONTACT2 = 'contact2';
const STORE_DET_KEY_LOGO = 'logo';
const STORE_DET_KEY_SOCIAL_INSTAGRAM = 'instagram';
const STORE_DET_KEY_SOCIAL_TELEGRAM = 'telegram';
const STORE_DET_KEY_SOCIAL_WHATSAPP = 'whatsapp';

const STORE_DETAILS_NOT_SET = 'وارد نشده';
const STORE_DETAILS_IS_SET = 'وارد شده';
const STORE_DETAILS_REMOVE_KEYWORD = 'remove';

const WATERMARK_QR_LOGO_PATH = '/public/assets/logo-qr.png';
const TELEGRAM_LINK_QR_FORMAT = 'png';
const TELEGRAM_LINK_QR_SIZE = 450;
const TELEGRAM_LINK = 'https://t.me/';
const TELEGRAM_START_STORE_COMMAND = 'start';

const TELEGRAM_SHOP_AVATAR_EXTENSION = 'jpg';
const TELEGRAM_SHOP_AVATAR_FINAL_WIDTH = 450;
const TELEGRAM_SHOP_AVATAR_FINAL_HEIGHT = 450;
const TELEGRAM_SHOP_AVATAR_FINAL_QUALITY = 80;

const CATEGORY_COUNT_MAX = 15;
const PAGINATION_LISTS = 2; // TODO - change this on production

