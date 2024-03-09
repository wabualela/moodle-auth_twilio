<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English language pack for Twilio
 *
 * @package    auth_twilio
 * @category   string
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'تويلو واتساب';
$string['notenabled'] = 'آسف ، لم يتم تمكين المكون الإضافي لمصادقة تويلو (واتساب)';
// Auth file
$string['whatsapp'] = 'واتساب';


// Settings page
$string['auth_emaildescription']      = '<p> ميزة التسجيل الذاتي المستندة إلى WhatsApp ، تمكّن المستخدمين من إنشاء حساباتهم الخاصة من خلال زر إنشاء حساب جديد بسيط في صفحة تسجيل الدخول.عند بدء العملية ، يتلقى المستخدمون على الفور رسالة WhatsApp التي تحتوي على رمز فريد ، مما يتيح لهم التحقق من حسابهم وتفعيله بسرعة.تتم إدارة محاولات تسجيل الدخول اللاحقة بسلاسة عن طريق الإشارة إلى رقم الهاتف المقدم ورمز التحقق مع البيانات المخزنة في قاعدة بيانات Moodle. </p>';
$string['auth_accountsid']            = 'رقم الحساب';
$string['auth_accountsiddescription'] = 'رقم حساب تويلو';
$string['auth_token']                 = 'التوكن';
$string['auth_tokendescription']      = 'الرمز المستخدم في التشفير';
$string['auth_servicesid']            = 'رقم الخدمة';
$string['auth_servicesiddescription'] = 'رقم الخدمة من حساب تويلو';

//  login page
$string['validate']               = 'ارسل';
$string['telplaceholder']         = 'أدخل رقم هاتفك';
$string['telinvalidfeedback']     = 'الرجاء ادخال رقم جوال صحيح';
$string['valide']                 = '✓ صالح';
$string['notvalidtel']            = 'ليس رقم هاتف صالح';
$string['enterotp']               = 'أدخل رمز OTP الخاص بك';
$string['fullnameforcertificate'] = 'الاسم الكامل للشهادة';
$string['otphelp']                = 'تم إرسال كلمة مرور مرة واحدة عبر واتساب إلى {$a}';



