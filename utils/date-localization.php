<?php
class HbDateLocalization {

    public $locale;

    public function __construct() {
		$this->locale = array(

            'af' => array(
                'month_names' => array( 'Januarie','Februarie','Maart','April','Mei','Junie','Julie','Augustus','September','Oktober','November','Desember' ),
                'day_names' => array( 'Sondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrydag', 'Saterdag' ),
                'day_names_min' => array( 'So','Ma','Di','Wo','Do','Vr','Sa' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'am' => array(
                'month_names' => array( 'ጃንዋሪ','ፈብርዋሪ','ማርች','አፕሪል','ሜይ','ጁን','ጁላይ','ኦገስት','ሴፕቴምበር','ኦክቶበር','ኖቬምበር','ዲሴምበር' ),
                'day_names' => array( 'ሰንዴይ', 'መንዴይ', 'ትዩስዴይ', 'ዌንስዴይ', 'ተርሰዴይ', 'ፍራይዴይ', 'ሳተርዴይ' ),
                'day_names_min' => array( 'ሰን', 'መን', 'ትዩ', 'ዌን', 'ተር', 'ፍራ', 'ሳተ' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'ar_DZ' => array(
                'month_names' => array( 'جانفي', 'فيفري', 'مارس', 'أفريل', 'ماي', 'جوان','جويلية', 'أوت', 'سبتمبر','أكتوبر', 'نوفمبر', 'ديسمبر' ),
                'day_names' => array( 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت' ),
                'day_names_min' => array( 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '6',
                'is_rtl' => 'true',
            ),

            'ar_EG' => array(
                'month_names' => array( 'يناير', 'فبراير', 'مارس', 'إبريل', 'مايو', 'يونية','يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر' ),
                'day_names' => array(  ),
                'day_names_min' => array( 'أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة', 'سبت' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '6',
                'is_rtl' => 'true',
            ),

            'ar' => array(
                'month_names' => array( 'كانون الثاني', 'شباط', 'آذار', 'نيسان', 'آذار', 'حزيران','تموز', 'آب', 'أيلول', 'تشرين الأول', 'تشرين الثاني', 'كانون الأول' ),
                'day_names' => array( 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت' ),
                'day_names_min' => array( 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '6',
                'is_rtl' => 'true',
            ),

            'az' => array(
                'month_names' => array( 'Yanvar','Fevral','Mart','Aprel','May','İyun','İyul','Avqust','Sentyabr','Oktyabr','Noyabr','Dekabr' ),
                'day_names' => array( 'Bazar','Bazar ertəsi','Çərşənbə axşamı','Çərşənbə','Cümə axşamı','Cümə','Şənbə' ),
                'day_names_min' => array( 'B','B','Ç','С','Ç','C','Ş' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'bg' => array(
                'month_names' => array( 'Януари','Февруари','Март','Април','Май','Юни','Юли','Август','Септември','Октомври','Ноември','Декември' ),
                'day_names' => array( 'Неделя','Понеделник','Вторник','Сряда','Четвъртък','Петък','Събота' ),
                'day_names_min' => array( 'Не','По','Вт','Ср','Че','Пе','Съ' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'bs' => array(
                'month_names' => array( 'Januar','Februar','Mart','April','Maj','Juni','Juli','August','Septembar','Oktobar','Novembar','Decembar' ),
                'day_names' => array( 'Nedelja','Ponedeljak','Utorak','Srijeda','Četvrtak','Petak','Subota' ),
                'day_names_min' => array( 'Ne','Po','Ut','Sr','Če','Pe','Su' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'ca' => array(
                'month_names' => array( 'Gener','Febrer','Mar&ccedil;','Abril','Maig','Juny','Juliol','Agost','Setembre','Octubre','Novembre','Desembre' ),
                'day_names' => array( 'Diumenge','Dilluns','Dimarts','Dimecres','Dijous','Divendres','Dissabte' ),
                'day_names_min' => array( 'Dg','Dl','Dt','Dc','Dj','Dv','Ds' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'cs' => array(
                'month_names' => array( 'leden','únor','březen','duben','květen','červen','červenec','srpen','září','říjen','listopad','prosinec' ),
                'day_names' => array( 'neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota' ),
                'day_names_min' => array( 'ne','po','út','st','čt','pá','so' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'cy' => array(
                'month_names' => array( 'Ionawr','Chwefror','Mawrth','Ebrill','Mai','Mehefin','Gorffennaf','Awst','Medi','Hydref','Tachwedd','Rhagfyr' ),
                'day_names' => array( 'Dydd Sul', 'Dydd Llun', 'Dydd Mawrth', 'Dydd Mercher', 'Dydd Iau', 'Dydd Gwener', 'Dydd Sadwrn' ),
                'day_names_min' => array( 'Su','Ll','Ma','Me','Ia','Gw','Sa' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'da' => array(
                'month_names' => array( 'Januar','Februar','Marts','April','Maj','Juni',        'Juli','August','September','Oktober','November','December' ),
                'day_names' => array( 'Søndag','Mandag','Tirsdag','Onsdag','Torsdag','Fredag','Lørdag' ),
                'day_names_min' => array( 'Sø','Ma','Ti','On','To','Fr','Lø' ),
                'date_format' => 'dd-mm-yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'de_CH' => array(
                'month_names' => array( 'Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember' ),
                'day_names' => array( 'Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag' ),
                'day_names_min' => array( 'So','Mo','Di','Mi','Do','Fr','Sa' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'de' => array(
                'month_names' => array( 'Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember' ),
                'day_names' => array( 'Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag' ),
                'day_names_min' => array( 'So','Mo','Di','Mi','Do','Fr','Sa' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'el' => array(
                'month_names' => array( 'Ιανουάριος','Φεβρουάριος','Μάρτιος','Απρίλιος','Μάιος','Ιούνιος','Ιούλιος','Αύγουστος','Σεπτέμβριος','Οκτώβριος','Νοέμβριος','Δεκέμβριος' ),
                'day_names' => array( 'Κυριακή','Δευτέρα','Τρίτη','Τετάρτη','Πέμπτη','Παρασκευή','Σάββατο' ),
                'day_names_min' => array( 'Κυ','Δε','Τρ','Τε','Πε','Πα','Σα' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'en_AU' => array(
                'month_names' => array( 'January','February','March','April','May','June','July','August','September','October','November','December' ),
                'day_names' => array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
                'day_names_min' => array( 'Su','Mo','Tu','We','Th','Fr','Sa' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'en_GB' => array(
                'month_names' => array( 'January','February','March','April','May','June','July','August','September','October','November','December' ),
                'day_names' => array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
                'day_names_min' => array( 'Su','Mo','Tu','We','Th','Fr','Sa' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'en_NZ' => array(
                'month_names' => array( 'January','February','March','April','May','June','July','August','September','October','November','December' ),
                'day_names' => array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
                'day_names_min' => array( 'Su','Mo','Tu','We','Th','Fr','Sa' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'en_US' => array(
                'month_names' => array( 'January','February','March','April','May','June','July','August','September','October','November','December' ),
                'day_names' => array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ),
                'day_names_min' => array( 'Su','Mo','Tu','We','Th','Fr','Sa' ),
                'date_format' => 'mm/dd/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'eo' => array(
                'month_names' => array( 'Januaro','Februaro','Marto','Aprilo','Majo','Junio','Julio','Aŭgusto','Septembro','Oktobro','Novembro','Decembro' ),
                'day_names' => array( 'Dimanĉo','Lundo','Mardo','Merkredo','Ĵaŭdo','Vendredo','Sabato' ),
                'day_names_min' => array( 'Di','Lu','Ma','Me','Ĵa','Ve','Sa' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'es_AR' => array(
                'month_names' => array( 'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre' ),
                'day_names' => array( 'Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado' ),
                'day_names_min' => array( 'Do','Lu','Ma','Mi','Ju','Vi','Sá' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'es_PE' => array(
                'month_names' => array( 'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre' ),
                'day_names' => array( 'Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado' ),
                'day_names_min' => array( 'Do','Lu','Ma','Mi','Ju','Vi','Sa' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'es' => array(
                'month_names' => array( 'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre' ),
                'day_names' => array( 'Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado' ),
                'day_names_min' => array( 'Do','Lu','Ma','Mi','Ju','Vi','Sá' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'et' => array(
                'month_names' => array( 'Jaanuar','Veebruar','Märts','Aprill','Mai','Juuni', 'Juuli','August','September','Oktoober','November','Detsember' ),
                'day_names' => array( 'Pühapäev', 'Esmaspäev', 'Teisipäev', 'Kolmapäev', 'Neljapäev', 'Reede', 'Laupäev' ),
                'day_names_min' => array( 'P','E','T','K','N','R','L' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'eu' => array(
                'month_names' => array( 'Urtarrila','Otsaila','Martxoa','Apirila','Maiatza','Ekaina','Uztaila','Abuztua','Iraila','Urria','Azaroa','Abendua' ),
                'day_names' => array( 'Igandea','Astelehena','Asteartea','Asteazkena','Osteguna','Ostirala','Larunbata' ),
                'day_names_min' => array( 'Ig','As','As','As','Os','Os','La' ),
                'date_format' => 'yyyy/mm/dd',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'fa' => array(
                'month_names' => array( 'فروردين','ارديبهشت','خرداد','تير','مرداد','شهريور','مهر','آبان','آذر','دي','بهمن','اسفند' ),
                'day_names' => array( 'يکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه' ),
                'day_names_min' => array( 'ي','د','س','چ','پ','ج', 'ش' ),
                'date_format' => 'yyyy/mm/dd',
                'first_day' => '6',
                'is_rtl' => 'true',
            ),

            'fi' => array(
                'month_names' => array( 'Tammikuu','Helmikuu','Maaliskuu','Huhtikuu','Toukokuu','Kes&auml;kuu',        'Hein&auml;kuu','Elokuu','Syyskuu','Lokakuu','Marraskuu','Joulukuu' ),
                'day_names' => array( 'Sunnuntai','Maanantai','Tiistai','Keskiviikko','Torstai','Perjantai','Lauantai' ),
                'day_names_min' => array( 'Su','Ma','Ti','Ke','To','Pe','La' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'fo' => array(
                'month_names' => array( 'Januar','Februar','Mars','Apríl','Mei','Juni','Juli','August','September','Oktober','November','Desember' ),
                'day_names' => array( 'Sunnudagur','Mánadagur','Týsdagur','Mikudagur','Hósdagur','Fríggjadagur','Leyardagur' ),
                'day_names_min' => array( 'Su','Má','Tý','Mi','Hó','Fr','Le' ),
                'date_format' => 'dd-mm-yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'fr_CH' => array(
                'month_names' => array( 'Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre' ),
                'day_names' => array( 'Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi' ),
                'day_names_min' => array( 'Di','Lu','Ma','Me','Je','Ve','Sa' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'fr' => array(
                'month_names' => array( 'Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre' ),
                'day_names' => array( 'Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi' ),
                'day_names_min' => array( 'Di','Lu','Ma','Me','Je','Ve','Sa' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'gl' => array(
                'month_names' => array( 'Xaneiro','Febreiro','Marzo','Abril','Maio','Xuño','Xullo','Agosto','Setembro','Outubro','Novembro','Decembro' ),
                'day_names' => array( 'Domingo','Luns','Martes','Mércores','Xoves','Venres','Sábado' ),
                'day_names_min' => array( 'Do','Lu','Ma','Me','Xo','Ve','Sá' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'gu' => array(
                'month_names' => array( 'જાન્યુઆરી','ફેબ્રુઆરી','માર્ચ','એપ્રિલ','મે','જૂન','જુલાઈ','ઑગસ્ટ','સપ્ટેમ્બર','ઑક્ટોબર','નવેમ્બર','ડિસેમ્બર' ),
                'day_names' => array( 'રવિવાર','સોમવાર','મંગળવાર','બુધવાર','ગુરુવાર','શુક્રવાર','શનિવાર' ),
                'day_names_min' => array( 'ર','સો','મં','બુ','ગુ','શુ','શ' ),
                'date_format' => 'dd-mm-yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'he' => array(
                'month_names' => array( 'ינואר','פברואר','מרץ','אפריל','מאי','יוני','יולי','אוגוסט','ספטמבר','אוקטובר','נובמבר','דצמבר' ),
                'day_names' => array( 'ראשון','שני','שלישי','רביעי','חמישי','שישי','שבת' ),
                'day_names_min' => array( 'א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'true',
            ),

            'hi_IN' => array(
                'month_names' => array( 'जनवरी',' फरवरी', 'मार्च', 'अप्रैल', 'मई', 'जून','जुलाई', 'अगस्त', 'सितम्बर', 'अक्टूबर', 'नवम्बर', 'दिसम्बर' ),
                'day_names' => array( 'रविवार', 'सोमवार', 'मंगलवार', 'बुधवार', 'गुरुवार', 'शुक्रवार', 'शनिवार' ),
                'day_names_min' => array( 'र','सो','मं','बु','गु','शु','श' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'hi' => array(
                'month_names' => array( 'जनवरी','फरवरी','मार्च','अप्रैल','मई','जून', 'जुलाई','अगस्त','सितंबर','अक्टूबर','नवंबर','दिसंबर' ),
                'day_names' => array( 'रविवार', 'सोमवार', 'मंगलवार', 'बुधवार', 'बृहस्पतिवार', 'शुक्रवार', 'शनिवार' ),
                'day_names_min' => array( 'रवि', 'सोम', 'मंगल', 'बुध', 'बृहस्पत', 'शुक्र', 'शनि' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'hr' => array(
                'month_names' => array( 'Siječanj','Veljača','Ožujak','Travanj','Svibanj','Lipanj','Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac' ),
                'day_names' => array( 'Nedjelja','Ponedjeljak','Utorak','Srijeda','Četvrtak','Petak','Subota' ),
                'day_names_min' => array( 'Ne','Po','Ut','Sr','Če','Pe','Su' ),
                'date_format' => 'dd.mm.yyyy.',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'hu' => array(
                'month_names' => array( 'Január', 'Február', 'Március', 'Április', 'Május', 'Június','Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December' ),
                'day_names' => array( 'Vasárnap', 'Hétfö', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat' ),
                'day_names_min' => array( 'V', 'H', 'K', 'Sze', 'Cs', 'P', 'Szo' ),
                'date_format' => 'yyyy-mm-dd',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'hy' => array(
                'month_names' => array( 'Հունվար','Փետրվար','Մարտ','Ապրիլ','Մայիս','Հունիս','Հուլիս','Օգոստոս','Սեպտեմբեր','Հոկտեմբեր','Նոյեմբեր','Դեկտեմբեր' ),
                'day_names' => array( 'կիրակի','եկուշաբթի','երեքշաբթի','չորեքշաբթի','հինգշաբթի','ուրբաթ','շաբաթ' ),
                'day_names_min' => array( 'կիր','երկ','երք','չրք','հնգ','ուրբ','շբթ' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'id' => array(
                'month_names' => array( 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember' ),
                'day_names' => array( 'Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu' ),
                'day_names_min' => array( 'Mg','Sn','Sl','Rb','Km','jm','Sb' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'is' => array(
                'month_names' => array( 'Jan&uacute;ar','Febr&uacute;ar','Mars','Apr&iacute;l','Ma&iacute','J&uacute;n&iacute;','J&uacute;l&iacute;','&Aacute;g&uacute;st','September','Okt&oacute;ber','N&oacute;vember','Desember' ),
                'day_names' => array( 'Sunnudagur','M&aacute;nudagur','&THORN;ri&eth;judagur','Mi&eth;vikudagur','Fimmtudagur','F&ouml;studagur','Laugardagur' ),
                'day_names_min' => array( 'Su','M&aacute;','&THORN;r','Mi','Fi','F&ouml;','La' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'it' => array(
                'month_names' => array( 'Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre' ),
                'day_names' => array( 'Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato' ),
                'day_names_min' => array( 'Do','Lu','Ma','Me','Gi','Ve','Sa' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'ja' => array(
                'month_names' => array( '1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月' ),
                'day_names' => array( '日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日' ),
                'day_names_min' => array( '日','月','火','水','木','金','土' ),
                'date_format' => 'yyyy/mm/dd',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'ka' => array(
                'month_names' => array( 'იანვარი','თებერვალი','მარტი','აპრილი','მაისი','ივნისი','ივლისი','აგვისტო','სექტემბერი','ოქტომბერი','ნოემბერი','დეკემბერი' ),
                'day_names' => array( 'კვირა', 'ორშაბათი', 'სამშაბათი', 'ოთხშაბათი', 'ხუთშაბათი', 'პარასკევი', 'შაბათი' ),
                'day_names_min' => array( 'კვ','ორ','სმ','ოთ', 'ხშ', 'პრ','შბ' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'km' => array(
                'month_names' => array( 'ខែ​មករា','ខែ​កុម្ភៈ','ខែ​មិនា','ខែ​មេសា','ខែ​ឧសភា','ខែ​មិថុនា','ខែ​កក្កដា','ខែ​សីហា','ខែ​កញ្ញា','ខែ​តុលា','ខែ​វិច្ឆិកា','ខែ​ធ្នូ' ),
                'day_names' => array( 'ថ្ងៃ​អាទិត្យ', 'ថ្ងៃ​ចន្ទ', 'ថ្ងៃ​អង្គារ', 'ថ្ងៃ​ពុធ', 'ថ្ងៃ​ព្រហស្បត្តិ៍', 'ថ្ងៃ​សុក្រ', 'ថ្ងៃ​សៅរ៍' ),
                'day_names_min' => array( 'អា','ច','អ','ពុ','ព្រ','សុ','ស' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'ko' => array(
                'month_names' => array( '1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월' ),
                'day_names' => array( '일요일','월요일','화요일','수요일','목요일','금요일','토요일' ),
                'day_names_min' => array( '일','월','화','수','목','금','토' ),
                'date_format' => 'yyyy-mm-dd',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'lt' => array(
                'month_names' => array( 'Sausis','Vasaris','Kovas','Balandis','Gegužė','Birželis','Liepa','Rugpjūtis','Rugsėjis','Spalis','Lapkritis','Gruodis' ),
                'day_names' => array( 'sekmadienis','pirmadienis','antradienis','trečiadienis','ketvirtadienis','penktadienis','šeštadienis' ),
                'day_names_min' => array( 'Se','Pr','An','Tr','Ke','Pe','Še' ),
                'date_format' => 'yyyy-mm-dd',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'lv' => array(
                'month_names' => array( 'Janvāris','Februāris','Marts','Aprīlis','Maijs','Jūnijs','Jūlijs','Augusts','Septembris','Oktobris','Novembris','Decembris' ),
                'day_names' => array( 'svētdiena','pirmdiena','otrdiena','trešdiena','ceturtdiena','piektdiena','sestdiena' ),
                'day_names_min' => array( 'Sv','Pr','Ot','Tr','Ct','Pk','Ss' ),
                'date_format' => 'dd-mm-yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'me_ME' => array(
                'month_names' => array( 'Januar','Februar','Mart','April','Maj','Jun','Jul','Avgust','Septembar','Oktobar','Novembar','Decembar' ),
                'day_names' => array( 'Neđelja', 'Poneđeljak', 'Utorak', 'Srijeda', 'Četvrtak', 'Petak', 'Subota' ),
                'day_names_min' => array( 'Ne','Po','Ut','Sr','Če','Pe','Su' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'me' => array(
                'month_names' => array( 'Јануар','Фебруар','Март','Април','Мај','Јун','Јул','Август','Септембар','Октобар','Новембар','Децембар' ),
                'day_names' => array( 'Неђеља', 'Понеђељак', 'Уторак', 'Сриједа', 'Четвртак', 'Петак', 'Субота' ),
                'day_names_min' => array( 'Не','По','Ут','Ср','Че','Пе','Су' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'mk' => array(
                'month_names' => array( 'Јануари','Февруари','Март','Април','Мај','Јуни','Јули','Август','Септември','Октомври','Ноември','Декември' ),
                'day_names' => array( 'Недела', 'Понеделник', 'Вторник', 'Среда', 'Четврток', 'Петок', 'Сабота' ),
                'day_names_min' => array( 'Не','По','Вт','Ср','Че','Пе','Са' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'ml' => array(
                'month_names' => array( 'ജനുവരി','ഫെബ്രുവരി','മാര്‍ച്ച്','ഏപ്രില്‍','മേയ്','ജൂണ്‍','ജൂലൈ','ആഗസ്റ്റ്','സെപ്റ്റംബര്‍','ഒക്ടോബര്‍','നവംബര്‍','ഡിസംബര്‍' ),
                'day_names' => array( 'ഞായര്‍', 'തിങ്കള്‍', 'ചൊവ്വ', 'ബുധന്‍', 'വ്യാഴം', 'വെള്ളി', 'ശനി' ),
                'day_names_min' => array( 'ഞാ','തി','ചൊ','ബു','വ്യാ','വെ','ശ' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'ms' => array(
                'month_names' => array( 'Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember' ),
                'day_names' => array( 'Ahad','Isnin','Selasa','Rabu','Khamis','Jumaat','Sabtu' ),
                'day_names_min' => array( 'Ah','Is','Se','Ra','Kh','Ju','Sa' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'mt' => array(
                'month_names' => array( 'Jannar','Frar','Marzu','April','Mejju','Ġunju','Lulju','Awissu','Settembru','Ottubru','Novembru','Diċembru' ),
                'day_names' => array( 'Il-Ħadd', 'It-Tnejn', 'It-Tlieta', 'L-Erbgħa', 'Il-Ħamis', 'Il-Ġimgħa', 'Is-Sibt' ),
                'day_names_min' => array( 'Ħ','T','T','E','Ħ','Ġ','S' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'nb_NO' => array(
                'month_names' => array( 'Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember' ),
                'day_names' => array( 'Søndag','Mandag','Tirsdag','Onsdag','Torsdag','Fredag','Lørdag' ),
                'day_names_min' => array( 'Sø','Ma','Ti','On','To','Fr','Lø' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'nl_BE' => array(
                'month_names' => array( 'januari', 'februari', 'maart', 'april', 'mei', 'juni','juli', 'augustus', 'september', 'oktober', 'november', 'december' ),
                'day_names' => array( 'zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag' ),
                'day_names_min' => array( 'zo', 'ma', 'di', 'wo', 'do', 'vr', 'za' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'nl' => array(
                'month_names' => array( 'januari', 'februari', 'maart', 'april', 'mei', 'juni','juli', 'augustus', 'september', 'oktober', 'november', 'december' ),
                'day_names' => array( 'zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag' ),
                'day_names_min' => array( 'zo', 'ma', 'di', 'wo', 'do', 'vr', 'za' ),
                'date_format' => 'dd-mm-yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'pl' => array(
                'month_names' => array( 'Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień' ),
                'day_names' => array( 'Niedziela','Poniedzialek','Wtorek','Środa','Czwartek','Piątek','Sobota' ),
                'day_names_min' => array( 'N','Pn','Wt','Śr','Cz','Pt','So' ),
                'date_format' => 'yyyy-mm-dd',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'pt_BR' => array(
                'month_names' => array( 'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro' ),
                'day_names' => array( 'Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado' ),
                'day_names_min' => array( 'D','S','T','Q','Q','S','S' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'pt' => array(
                'month_names' => array( 'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro' ),
                'day_names' => array( 'Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado' ),
                'day_names_min' => array( 'D','S','T','Q','Q','S','S' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'rm' => array(
                'month_names' => array( 'Schaner','Favrer','Mars','Avrigl','Matg','Zercladur','Fanadur','Avust','Settember','October','November','December' ),
                'day_names' => array( 'Dumengia','Glindesdi','Mardi','Mesemna','Gievgia','Venderdi','Sonda' ),
                'day_names_min' => array( 'Du','Gl','Ma','Me','Gi','Ve','So' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'ro' => array(
                'month_names' => array( 'Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie' ),
                'day_names' => array( 'Duminică', 'Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sâmbătă' ),
                'day_names_min' => array( 'Du','Lu','Ma','Mi','Jo','Vi','Sâ' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'ru' => array(
                'month_names' => array( 'Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь' ),
                'day_names' => array( 'воскресенье','понедельник','вторник','среда','четверг','пятница','суббота' ),
                'day_names_min' => array( 'Вс','Пн','Вт','Ср','Чт','Пт','Сб' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'sk' => array(
                'month_names' => array( 'Január','Február','Marec','Apríl','Máj','Jún','Júl','August','September','Október','November','December' ),
                'day_names' => array( 'Nedel\'a','Pondelok','Utorok','Streda','Štvrtok','Piatok','Sobota' ),
                'day_names_min' => array( 'Ne','Po','Ut','St','Št','Pia','So' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'sl' => array(
                'month_names' => array( 'Januar','Februar','Marec','April','Maj','Junij','Julij','Avgust','September','Oktober','November','December' ),
                'day_names' => array( 'Nedelja','Ponedeljek','Torek','Sreda','&#x10C;etrtek','Petek','Sobota' ),
                'day_names_min' => array( 'Ne','Po','To','Sr','&#x10C;e','Pe','So' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'sq' => array(
                'month_names' => array( 'Janar','Shkurt','Mars','Prill','Maj','Qershor','Korrik','Gusht','Shtator','Tetor','Nëntor','Dhjetor' ),
                'day_names' => array( 'E Diel','E Hënë','E Martë','E Mërkurë','E Enjte','E Premte','E Shtune' ),
                'day_names_min' => array( 'Di','Hë','Ma','Më','En','Pr','Sh' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'sr_SR' => array(
                'month_names' => array( 'Januar','Februar','Mart','April','Maj','Jun','Jul','Avgust','Septembar','Oktobar','Novembar','Decembar' ),
                'day_names' => array( 'Nedelja','Ponedeljak','Utorak','Sreda','Četvrtak','Petak','Subota' ),
                'day_names_min' => array( 'Ne','Po','Ut','Sr','Če','Pe','Su' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'sr' => array(
                'month_names' => array( 'Јануар','Фебруар','Март','Април','Мај','Јун','Јул','Август','Септембар','Октобар','Новембар','Децембар' ),
                'day_names' => array( 'Недеља','Понедељак','Уторак','Среда','Четвртак','Петак','Субота' ),
                'day_names_min' => array( 'Не','По','Ут','Ср','Че','Пе','Су' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'sv' => array(
                'month_names' => array( 'Januari','Februari','Mars','April','Maj','Juni',        'Juli','Augusti','September','Oktober','November','December' ),
                'day_names' => array( 'Söndag','Måndag','Tisdag','Onsdag','Torsdag','Fredag','Lördag' ),
                'day_names_min' => array( 'Sö','Må','Ti','On','To','Fr','Lö' ),
                'date_format' => 'yyyy-mm-dd',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'ta' => array(
                'month_names' => array( 'தை','மாசி','பங்குனி','சித்திரை','வைகாசி','ஆனி','ஆடி','ஆவணி','புரட்டாசி','ஐப்பசி','கார்த்திகை','மார்கழி' ),
                'day_names' => array( 'ஞாயிற்றுக்கிழமை','திங்கட்கிழமை','செவ்வாய்க்கிழமை','புதன்கிழமை','வியாழக்கிழமை','வெள்ளிக்கிழமை','சனிக்கிழமை' ),
                'day_names_min' => array( 'ஞா','தி','செ','பு','வி','வெ','ச' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'th' => array(
                'month_names' => array( 'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม' ),
                'day_names' => array( 'อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์' ),
                'day_names_min' => array( 'อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'tr' => array(
                'month_names' => array( 'Ocak','Şubat','Mart','Nisan','Mayıs','Haziran','Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık' ),
                'day_names' => array( 'Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi' ),
                'day_names_min' => array( 'Pz','Pt','Sa','Ça','Pe','Cu','Ct' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'tt' => array(
                'month_names' => array( 'Гынвар','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь' ),
                'day_names' => array( 'якшәмбе','дүшәмбе','сишәмбе','чәршәмбе','пәнҗешәмбе','җомга','шимбә' ),
                'day_names_min' => array( 'Як','Дү','Си','Чә','Пә','Җо','Ши' ),
                'date_format' => 'dd.mm.yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'uk' => array(
                'month_names' => array( 'Січень','Лютий','Березень','Квітень','Травень','Червень','Липень','Серпень','Вересень','Жовтень','Листопад','Грудень' ),
                'day_names' => array( 'неділя','понеділок','вівторок','середа','четвер','п\'ятниця','субота' ),
                'day_names_min' => array( 'Нд','Пн','Вт','Ср','Чт','Пт','Сб' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'ur' => array(
                'month_names' => array( 'جنوری','فروری','مارچ','اپریل','مئی','جون','جولائی','اگست','ستمبر','اکتوبر','نومبر','دسمبر' ),
                'day_names' => array( 'اتوار','پير','منگل','بدھ','جمعرات','جمعہ','ہفتہ' ),
                'day_names_min' => array( 'اتوار','پير','منگل','بدھ','جمعرات','جمعہ','ہفتہ' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'true',
            ),

            'vi' => array(
                'month_names' => array( 'Tháng Một', 'Tháng Hai', 'Tháng Ba', 'Tháng Tư', 'Tháng Năm', 'Tháng Sáu','Tháng Bảy', 'Tháng Tám', 'Tháng Chín', 'Tháng Mười', 'Tháng Mười Một', 'Tháng Mười Hai' ),
                'day_names' => array( 'Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy' ),
                'day_names_min' => array( 'CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7' ),
                'date_format' => 'dd/mm/yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'zh_CN' => array(
                'month_names' => array( '一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月' ),
                'day_names' => array( '星期日','星期一','星期二','星期三','星期四','星期五','星期六' ),
                'day_names_min' => array( '日','一','二','三','四','五','六' ),
                'date_format' => 'yyyy-mm-dd',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),

            'zh_HK' => array(
                'month_names' => array( '一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月' ),
                'day_names' => array( '星期日','星期一','星期二','星期三','星期四','星期五','星期六' ),
                'day_names_min' => array( '日','一','二','三','四','五','六' ),
                'date_format' => 'dd-mm-yyyy',
                'first_day' => '0',
                'is_rtl' => 'false',
            ),

            'zh_TW' => array(
                'month_names' => array( '一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月' ),
                'day_names' => array( '星期日','星期一','星期二','星期三','星期四','星期五','星期六' ),
                'day_names_min' => array( '日','一','二','三','四','五','六' ),
                'date_format' => 'yyyy/mm/dd',
                'first_day' => '1',
                'is_rtl' => 'false',
            ),


        );
	}

}