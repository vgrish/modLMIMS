<?php
/**
 * Settings Russian Lexicon Entries for modLMIMS
 *
 * @package modLMIMS
 * @subpackage lexicon
 */
$_lang['area_modlmims_area'] = 'modLMIMS';

$_lang['setting_modlmims.refresh_on_page_save'] = 'Обновлять данные о ресурсе<br>при его сохранении/изменении?';
$_lang['setting_modlmims.refresh_on_page_save_desc'] = 'Если эта настройка включена, то компонент будет загружать страницу из web (с помощью <code>curl</code> или <code>file_get_contents</code>), чтобы обновить данные о ней.
<br><br>
Выключите эту настройку, если:<br>
- на вашем сайте установлена http-авторизация;<br>
- страницы вашего сайта открываются слишком медленно.';

$_lang['setting_modlmims.remove_related_lmims_on_empty_trash'] = 'Удалять связанные данные при очистке корзины?';
$_lang['setting_modlmims.remove_related_lmims_on_empty_trash_desc'] = 'Если эта настройка включена, то компонент будет удалять все записи, которые относятся к удаляемым ресурсам. ';
