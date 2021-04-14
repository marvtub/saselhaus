<?php
class sf28c_BuildSOQL
{
    private $fields;
    private $object;
    private $order;
    private $by;
    private $n;
    private $filter;
    private $query;
    public $valid;

    private $offset;

    private $calendarFields;

    function getQuery()
    {

        $where = '';
        $offset = '';

        if (($this->filter) != '')
        {
            $this->filter = str_replace('&quot;', '"', $this->filter);
            $this->filter = str_replace('&#091;', '[', $this->filter);
            $this->filter = str_replace('&#093;', ']', $this->filter);
            $this->filter = str_replace('&#092;', '\\', $this->filter);

            $where = ' WHERE ' . wp_specialchars_decode($this->filter);

        }

        if (($this->offset) != '')
        {

            $offset = ' OFFSET ' . $this->offset;

        }

        // Retreive IDs for Detail Layouts
        $tempfieldsarray = array_map('trim', explode( ",", $this->fields ));


        if ( !in_array( "Id", $tempfieldsarray ) )
            array_push( $tempfieldsarray, "Id" );

        $this->fields = implode( ",", $tempfieldsarray );



        $this->query = 'SELECT ' . $this->fields . ' FROM ' . $this->object . $where . ' ORDER BY ' . $this->by . ' ' . $this->order . ' LIMIT ' . $this->n . $offset;

        return $this->query;
    }

    function __construct($atts)
    {
        $this->valid = true;

        if (!isset($atts['fields']) || !isset($atts['o']) || !isset($atts['by']) || !isset($atts['n']))
        {
            $this->valid = false;
        }
        else
        {
            $this->fields = $atts['fields'];
            $this->object = $atts['o'];
            $this->by = $atts['by'];
            $this->n = $atts['n'];

            //Optional Fields in SOQL
            $this->order = 'ASC';
            if (isset($atts['order'])) $this->order = $atts['order'];

            $this->filter = null;
            if (isset($atts['filter'])) $this->filter = $atts['filter'];

            $this->offset = null;
            if (isset($atts['offset'])) $this->offset = $atts['offset'];

        }

    }
}

class sf28c_Transient
{
    public $objectName;
    public $fieldsArray;
    public $currentLabelsArray;

    function set()
    {
        set_transient('sf28c_' . $this->objectName, json_encode($this->currentLabelsArray) , 86400);
    }

    function get($transientName)
    {
        $transient = get_transient($transientName);

        if ($transient !== false) $transient = json_decode($transient);

        return $transient;
    }

    function newFieldExists()
    {

        if (count(array_diff($this->fieldsArray, array_keys($this->currentLabelsArray))) === 0) return false;
        else return true;
    }

    function getSFLabels()
    {
        $fieldsget = new sf28c_Curl();
        $fieldresponse = $fieldsget->getCurl('fields', array(
            'object' => $this->objectName
        ));

        $this->currentLabelsArray = sf28c_Response::format($fieldresponse, 'fields');
        return $this->currentLabelsArray;
    }

    function setCurrentLabels()
    {
        if ($this->get('sf28c_' . $this->objectName))
        {
            $this->currentLabelsArray = (array)$this->get('sf28c_' . $this->objectName);

            if ($this->newFieldExists())
            {
                $this->getSFLabels();
                $this->set();
            }
        }
        else
        {
            $this->getSFLabels();
            $this->set();
        }

    }

    function __construct($objectNameatt, $fieldsArray)
    {
        $this->objectName = $objectNameatt;
        $this->fieldsArray = $fieldsArray;

    }
}

class sf28c_AddFilter
{
    public $fieldsArray;
    public $labels;

    function getScript($pagination = null)
    {

        $pageoptions = '';

        if ($pagination != null)
        {
            $pageoptions = 'page: ' . $pagination . ',
			pagination: true,
			outerWindow: 2';
        }

        return '
		var sf28_userList' . sf28c_Count::get() . ';
		jQuery( document ).ready(function() {
			jQuery( "input[class*=\'sforc-search\']" ).focus();

			var options = {
				valueNames: ["' . implode('","', $this->fieldsArray) . '"],

				' . $pageoptions . '

			};

			sf28_userList' . sf28c_Count::get() . ' = new List("sforc-wrap-' . sf28c_Count::get() . '", options);
		});';
    }

    function getFilterBox()
    {
        return '<input class="search sforc-search" placeholder="Search by ' . $this->labels . '" />';
    }

    function __construct($fieldsArray, $currentLabelsArray)
    {

        foreach ($fieldsArray as $key => $field)
        {

            if ($field == end($fieldsArray)) $this->labels .= $currentLabelsArray[$field];
            else $this->labels .= $currentLabelsArray[$field] . ', ';

        }
        $this->fieldsArray = $fieldsArray;
    }
}

class sf28c_View
{
    private $records;
    private $fieldsArray;
    private $labelArray;
    public $firstField;
    public $calScriptArray;

    function getCrossObjectField( $fieldname, $multilevelfield )
    {
        
        $fieldlevels = explode('.', $fieldname);

        for( $i = 0; $i< sizeof($fieldlevels); $i++ )
        {
            $multilevelfield = $multilevelfield[$fieldlevels[$i]];
        }          

        return $multilevelfield;
    }

    function processCrossObjectFields()
    {

        foreach ( (array)$this->records as $key => $record )
        {

            foreach ((array)$this->fieldsArray as $field)
            {

                if( !isset($record[$field]) && $this->getCrossObjectField( $field, $record ) )
                {
                    $this->records[$key][$field] = $this->getCrossObjectField( $field, $record ) ;  
                    $this->labelArray[$field] = $field;   

                 }
            }

        }


    }

    function AsJSON()
    {

        $print_json = '';
        $json_records = array();

        foreach ((array)$this->records as $record)
        {

            $json_single_record = array();

            foreach ((array)$this->fieldsArray as $field)
            {

                $json_single_record[$field] = $record[$field];

            }

            array_push($json_records, $json_single_record);

        }

        $print_json = json_encode($json_records);
        return $print_json;

    }

    function AsCards()
    {
        wp_enqueue_style('sforc_cards');

        $print_cards = '<div class="sforc-ui sforc-stackable sforc-cards list">'. PHP_EOL ;

        foreach ((array)$this->records as $record)
        {

            $print_cards = $print_cards . '<div class="sforc-card" data-sforclink="'.$record['Id'].'">'. PHP_EOL .
            '<div class="sforc-content">'. PHP_EOL .
            '<div class="sforc-header ' . $this->firstField . '">' . $record[$this->firstField] . '</div>'. PHP_EOL .
            '</div>'. PHP_EOL .'<div class="sforc-content sforc-description-content">'. PHP_EOL ;

            foreach ((array)$this->fieldsArray as $field)
            {
                if ($field != reset($this->fieldsArray)) //Skip the first field
                
                {
                    $print_cards .= '<p class="sforc-label">' . $this->labelArray[$field] . '</p><span class="sforc-description ' . $field . '">' . $record[$field] . '</span>'. PHP_EOL .'<hr class="sforc-divider">'. PHP_EOL ;
                }
            }
            $print_cards .= '</div>'. PHP_EOL .
                        '</div>';
        }

        return $print_cards;
    }

    function AsTables()
    {

        wp_enqueue_style('sforc_table');
        wp_enqueue_script('sforc_table_js');

        $print_tables = '<table class="tablesaw" data-tablesaw-mode="stack">'. PHP_EOL .
        '<thead>'. PHP_EOL .
        '<tr>';
        foreach ((array)$this->fieldsArray as $field)
        {
            $print_tables .= '<th scope="col" data-tablesaw-priority="persist" class="sort" data-sort="' . $field . '"><b>' . $this->labelArray[$field] . '</b></th>';
        }
        $print_tables .= '</tr>'. PHP_EOL . 
        '</thead>'. PHP_EOL .
        '<tbody class="list">'. PHP_EOL ;

        foreach ((array)$this->records as $record)
        {
            $print_tables .= '<tr class="sforc-row" data-sforclink="'.$record['Id'].'">'. PHP_EOL ;
            foreach ((array)$this->fieldsArray as $field)
            {
                $print_tables .= '<td class="' . $field . '">' . $record[$field] . '</td>'. PHP_EOL ;
            }
            $print_tables .= '</tr>'. PHP_EOL ;
        }
        $print_tables .= '</tbody>'.PHP_EOL. 
        '</table>';

        return $print_tables;

    }

    function AsCalendar()
    {
        wp_enqueue_style('sforc_calendar');
        wp_enqueue_script('sforc_moment_js');
        wp_enqueue_script('sforc_calendar_js');

        $cal = array();
        $Categories = array();
        $CategoryField = '';

        $AddFilterSelectOptions = array();

        $selectCategory = '';
        $calEventRenderScript = '';
        $catChangeScript = '';

        $catRender = '';

        $selectFilters = '';
        $filterChangeScript = '';
        $addFilterRender = '';

        $calPopUpScript = '';

        $colorset = array(
            array(
                'fill' => '#009FFF',
                'border' => '#0084FF',
            ) ,
            array(
                'fill' => '#00C6A2',
                'border' => '#2FA18E',
            ) ,
            array(
                'fill' => '#FF0070',
                'border' => '#D0266D',
            ) ,
            array(
                'fill' => '#FFBD41',
                'border' => '#FF9900',
            ) ,
            array(
                'fill' => '#FF7649',
                'border' => '#F22900',
            ) ,
            array(
                'fill' => '#DD00A4',
                'border' => '#8F00D6',
            ) ,
            array(
                'fill' => '#5C13D4',
                'border' => '#2E0472',
            ) ,
            array(
                'fill' => '#005FFF',
                'border' => '#5C13D4',
            ) ,
            array(
                'fill' => '#383838',
                'border' => '#161616',
            ) ,
        );

        $colorJSON = json_encode($colorset);

        foreach ((array)$this->records as $recordkey => $record)
        {
            $endDayTimeorDay = '';
            $EventLink = '';

            if (isset($this->calendarFields['EventLinkPosition'])) $EventLink = $record[$this->fieldsArray[$this->calendarFields['EventLinkPosition']]];
            if (isset($this->calendarFields['EndDatePosition'])) $endDayTimeorDay = $record[$this->fieldsArray[$this->calendarFields['EndDatePosition']]];

            if (isset($this->calendarFields['CategoryPosition']))
            {

                $CategoryField = $this->fieldsArray[$this->calendarFields['CategoryPosition']];

                if (!in_array($record[$CategoryField], $Categories, true))
                {
                    array_push($Categories, $record[$CategoryField]);
                }

                $color = $colorset[array_search($record[$CategoryField], $Categories) % sizeof($colorset) ]; //Deprecated
                $colorIndex = array_search($record[$CategoryField], $Categories) % sizeof($colorset);

                array_push($cal, array(
                    'title' => $record[$this->firstField],
                    'start' => $record[$this->fieldsArray[1]],
                    'end' => $endDayTimeorDay,
                    'cat_' . $CategoryField => (string)$record[$CategoryField],
                    'colorType' => $colorIndex,
                    'url' => $EventLink
                ));

            }
            else array_push($cal, array(
                'title' => $record[$this->firstField],
                'start' => $record[$this->fieldsArray[1]],
                'end' => $endDayTimeorDay,
                'url' => $EventLink
            ));

            if (!empty($this->calendarFields['AddFilters']))
            {

                foreach ($this->calendarFields['AddFilters'] as $key => $value)
                {

                    $fieldname = 'filter_' . $this->fieldsArray[$value];

                    $cal[$recordkey] = $cal[$recordkey] + array(
                        $fieldname => (string)$record[$this->fieldsArray[$value]],
                    );

                    if (!isset($AddFilterOptions[$this->fieldsArray[$value]]))
                    {
                        $AddFilterOptions[$this->fieldsArray[$value]] = array();
                    }
                    if (!in_array($record[$this->fieldsArray[$value]], $AddFilterOptions[$this->fieldsArray[$value]], true))
                    {
                        array_push($AddFilterOptions[$this->fieldsArray[$value]], $record[$this->fieldsArray[$value]]);
                    }

                }

            }

            if (!empty($this->calendarFields['PopUpFields']))
            {
                $calPopUpScriptField = '';

                foreach ($this->calendarFields['PopUpFields'] as $key => $value)
                {

                    $fieldname = 'popup_' . $this->fieldsArray[$value];
                    $cal[$recordkey] = $cal[$recordkey] + array(
                        $fieldname => $record[$this->fieldsArray[$value]],
                    );
                    $calPopUpScriptField .= '<p class="sforc-tooltip-content"><span class="sforc-tooltip-label">' . $this->labelArray[$this->fieldsArray[$value]] . '</span><br><span class="sforc-tooltip-description">\'+calEvent.popup_' . $this->fieldsArray[$value] . '+\'</span></p>';

                }

                $calPopUpScript = ", eventMouseover: function(calEvent, jsEvent) {
					var sforcTooltip = '<span class=\"sforc-tooltip\" ><p class=\"sforce-tooltip-title\"><b>'+calEvent.title+'</b></p>'+'" . $calPopUpScriptField . "'+'</span>';
					var sforcTooltip = jQuery(sforcTooltip).appendTo('body');

					jQuery(this).mouseover(function(e) {
						jQuery(this).css('z-index', 1000);
						}).mousemove(function(element) {
							sforcTooltip.css('top', element.pageY + 10);
							sforcTooltip.css('left', element.pageX + 20);
							});
							},

							eventMouseout: function(calEvent, jsEvent) {
								jQuery(this).css('z-index', 10);
								jQuery('.sforc-tooltip').remove();
							},";

            }

        }

        $Categories = array_unique($Categories);

        if (isset($this->calendarFields['CategoryPosition']))
        {
            $catChangeScript = "
						jQuery('#calendar_category').on('change',function(){
							jQuery('#sforc-calendar').fullCalendar('rerenderEvents');
						})";

            $selectCategory = "<select class='sforc-select sforc-category' id='calendar_category'>
						<option id='calendar_cat_option_select' value='all'>Select " . $this->labelArray[$CategoryField] . "</option>
						<option id='calendar_cat_option_all' value='all'>All</option>";

            $Categories = array_filter($Categories);
            sort($Categories);

            foreach ($Categories as $key => $value)
            {

                $selectCategory .= "
							<option value='" . $value . "'>" . $value . "</option>";

            }

            $selectCategory .= "</select>";

            $catRender = " (jQuery('#calendar_category').val() === 'all' || event.cat_" . $CategoryField . ".indexOf(jQuery('#calendar_category').val()) >= 0) && ";

        }

        if (isset($this->calendarFields['AddFilters']))
        {

            foreach ($this->calendarFields['AddFilters'] as $key => $value)
            {

                $addFilterRender .= " (jQuery('#calendar_filter_" . $this->fieldsArray[$value] . "').val() === 'all' || event.filter_" . $this->fieldsArray[$value] . ".indexOf(jQuery('#calendar_filter_" . $this->fieldsArray[$value] . "').val()) >= 0) && ";

                $filterChangeScript .= "
							jQuery('#calendar_filter_" . $this->fieldsArray[$value] . "').on('change',function(){
								jQuery('#sforc-calendar').fullCalendar('rerenderEvents');
							})";

                $selectFilters .= "<select class='sforc-select sforc-select-filter' id='calendar_filter_" . $this->fieldsArray[$value] . "'>
							<option  id='calendar_filter_" . $this->fieldsArray[$value] . "_option_select' value='all'>Select " . $this->labelArray[$this->fieldsArray[$value]] . "</option>
							<option  id='calendar_filter_" . $this->fieldsArray[$value] . "_option_all'  value='all'>All</option>";

                $AddFilterOptions[$this->fieldsArray[$value]] = array_filter($AddFilterOptions[$this->fieldsArray[$value]]);
                sort($AddFilterOptions[$this->fieldsArray[$value]]);

                foreach ($AddFilterOptions[$this->fieldsArray[$value]] as $optkey => $options)
                {
                    $selectFilters .= "<option value='" . $options . "'>" . $options . "</option>";

                }

                $selectFilters .= "</select>";
            }

        }

        if (isset($this->calendarFields['AddFilters']) || isset($this->calendarFields['CategoryPosition']))
        {
            $calEventRenderScript = "
						, eventRender: function eventRender( event, element, view ) { return " . $catRender . $addFilterRender . " true}";

        }

        $hasCategories = false;

        if ($CategoryField !== '') $hasCategories = true;

        $this->calScriptArray = array(
            'catChangeScript' => $catChangeScript,
            'filterChangeScript' => $filterChangeScript,
            'calEvents' => $cal,
            'calEventRenderScript' => $calEventRenderScript,
            'calPopUpScript' => $calPopUpScript,
            'calColors' => $colorJSON,
            'hasCategories' => $hasCategories
        );

        $print_calendar = $selectCategory . $selectFilters . "<div id='sforc-calendar'></div>";

        return $print_calendar;
    }

    function AsCalendarAddScript()
    {
        $calScriptArray = $this->calScriptArray;

        $catChangeScript = $calScriptArray['catChangeScript'];
        $filterChangeScript = $calScriptArray['filterChangeScript'];
        $cal = $calScriptArray['calEvents'];
        $calEventRenderScript = $calScriptArray['calEventRenderScript'];
        $calPopUpScript = $calScriptArray['calPopUpScript'];
        $calColors = $calScriptArray['calColors'];

        $catSetEventColor = '';

        if ($calScriptArray['hasCategories'] === true) $catSetEventColor = "for (var key in sf28_" . sf28c_Count::get() . "CalEvents)
   				{
   					sf28_" . sf28c_Count::get() . "CalEvents[key]['backgroundColor'] = sf28_" . sf28c_Count::get() . "CalColors[sf28_" . sf28c_Count::get() . "CalEvents[key]['colorType']].fill;
   					sf28_" . sf28c_Count::get() . "CalEvents[key]['borderColor'] = sf28_" . sf28c_Count::get() . "CalColors[sf28_" . sf28c_Count::get() . "CalEvents[key]['colorType']].border;
   				}";

        return "
   				sf28_" . sf28c_Count::get() . "CalColors = " . $calColors . ";

   				sf28_" . sf28c_Count::get() . "CalOptions = {

   					themeSystem: 'standard',
   					timezone: 'local',
   					eventBorderColor: '#0084FF',
   					eventBackgroundColor: '#009FFF',
   					eventTextColor: 'white',
   					left: 'prev,next today',
   					center: 'title',
   					right: 'month,agendaWeek,agendaDay,listMonth',
   					firstDay: 0,
   					eventLimit: true,
   					defaultView: 'month',
   					defaultDate: moment().toDate() 
   				}

   				if(typeof myCalendarOptions !== 'undefined')
   				{			       				
   					for (var key in myCalendarOptions)
   					{
   						if (typeof myCalendarOptions[key] !== 'undefined')
   						{
   							sf28_" . sf28c_Count::get() . "CalOptions[key] = myCalendarOptions[key];
   						}
   					}
   				}

   				if(typeof myCalendarTheme !== 'undefined' && myCalendarTheme.length <= 9)
   				{			       				
   					for (var key in myCalendarTheme)
   					{
   						if (typeof myCalendarTheme[key].fill !== 'undefined' && typeof myCalendarTheme[key].border !== 'undefined')
   						{
   							sf28_" . sf28c_Count::get() . "CalColors[key].fill = myCalendarTheme[key].fill;
   							sf28_" . sf28c_Count::get() . "CalColors[key].border = myCalendarTheme[key].border;
   						}
   					}
   				}



   				sf28_" . sf28c_Count::get() . "CalEvents = " . json_encode($cal) . ";

   				" . $catSetEventColor . "

   				jQuery(document).ready(function() {
   					" . $catChangeScript . $filterChangeScript . "
   					jQuery('#sforc-calendar').fullCalendar({

   						themeSystem: sf28_" . sf28c_Count::get() . "CalOptions['themeSystem'],
   						header: 
   						{
   							left: sf28_" . sf28c_Count::get() . "CalOptions['left'],
   							center: sf28_" . sf28c_Count::get() . "CalOptions['center'],
   							right: sf28_" . sf28c_Count::get() . "CalOptions['right'],
   							},

   							timezone: sf28_" . sf28c_Count::get() . "CalOptions['timezone'],
   							eventBorderColor: sf28_" . sf28c_Count::get() . "CalOptions['eventBorderColor'],
   							eventBackgroundColor: sf28_" . sf28c_Count::get() . "CalOptions['eventBackgroundColor'],
   							eventTextColor: sf28_" . sf28c_Count::get() . "CalOptions['eventTextColor'],

   							firstDay: sf28_" . sf28c_Count::get() . "CalOptions['firstDay'],
   							defaultDate: sf28_" . sf28c_Count::get() . "CalOptions['defaultDate'],
   							defaultView: sf28_" . sf28c_Count::get() . "CalOptions['defaultView'], 

   							navLinks: true, // can click day/week names to navigate views
   							editable: false,
   							eventLimit: sf28_" . sf28c_Count::get() . "CalOptions['eventLimit'],

   							eventClick: function(event) {
   								if (event.url) {
   									window.open(event.url, '_blank');
   									return false;
   								}
   								},

   								events: sf28_" . sf28c_Count::get() . "CalEvents" . $calEventRenderScript . $calPopUpScript . "

   								});

   							});";

    }

    function __construct($records, $fieldsArray, $labelArray, $calendarFields = array())
    {
        $this->records = $records;
        $this->fieldsArray = $fieldsArray;
        $this->labelArray = $labelArray;
        $this->firstField = reset($fieldsArray);
        wp_enqueue_style('sforc_main');

        $this->processCrossObjectFields();
        $this->calendarFields = $calendarFields;
        $this->calScriptArray = array();

    }
}

class sf28c_Count
{
    private static $count = 0;

    static public function set()
    {
        self::$count += 1;
        return self::$count;
    }

    static public function get()
    {
        return self::$count;
    }

}

class sf28c_xtraCalendarFields
{

    private $EndDateAndCat;
    private $AddFilters = array();
    private $PopUpFields = array();
    private $EventLink;

    private $xtraFieldsArray = array();

    function setFields()
    {
        if (isset($this->EndDateAndCat[1]))
        {
            if ($this->EndDateAndCat[0] != 0) $this->xtraFieldsArray['EndDatePosition'] = $this->EndDateAndCat[0] - 1;
            if ($this->EndDateAndCat[1] != 0) $this->xtraFieldsArray['CategoryPosition'] = $this->EndDateAndCat[1] - 1;
        }

        if (!empty($this->AddFilters))
        {
            $this->xtraFieldsArray['AddFilters'] = array();
            foreach ($this->AddFilters as $key => $value)
            {

                array_push($this->xtraFieldsArray['AddFilters'], $value - 1);
            }

        }

        if (isset($this->EventLink))
        {
            if ($this->EventLink != 0) $this->xtraFieldsArray['EventLinkPosition'] = $this->EventLink - 1;

        }

        if (!empty($this->PopUpFields))
        {
            $this->xtraFieldsArray['PopUpFields'] = array();
            foreach ($this->PopUpFields as $key => $value)
            {

                array_push($this->xtraFieldsArray['PopUpFields'], $value - 1);
            }
        }

        return $this->xtraFieldsArray;
    }

    function __construct($atts)
    {

        if (isset($atts['c'])) $this->EndDateAndCat = $atts['c'];

        if (isset($atts['f'])) $this->AddFilters = str_split($atts['f']);

        if (isset($atts['p'])) $this->PopUpFields = str_split($atts['p']);

        if (isset($atts['k'])) $this->EventLink = $atts['k'];

    }
}

class sf28c_DynamicURLQuery
{
    private $att;
    private $pattern;

    function getQuery()
    {
        $URLmatches = array();
        preg_match_all($this->pattern, $this->att, $URLmatches, PREG_SET_ORDER);

        $URLatt = array();
        $enclosedURLatt = array();
        $URLreplacements = array();

        foreach ($URLmatches as $URLmatch)
        {

            array_push($URLatt, $URLmatch[0]);
            array_push($enclosedURLatt, '/{!' . $URLmatch[0] . '}/');

            if (isset($_GET[$URLmatch[0]])) array_push($URLreplacements, "'" . $_GET[$URLmatch[0]] . "'");
        }

        $this->att = preg_replace($enclosedURLatt, $URLreplacements, $this->att);

        return $this->att;
    }

    function __construct($att, $pattern)
    {
        $this->att = $att;
        $this->pattern = $pattern;
    }
}

function sforc_shortcode_show_records($atts, $content = null)
{

    $sf28c_pattern = '/(?<={!).*?(?=})/';

    $shortcodeout = '';
    $forceViewType = '';
    $calendarFields = array();

    sf28c_Count::set();

    if (isset($atts['filter']) && strpos($atts['filter'], '$WPId') !== false)
    {
        $userid = get_current_user_id();
        if ($userid === 0) return "Please login to view this info";
        else
        {
            $atts['filter'] = str_replace('$WPId', $userid, $atts['filter']);
        }
    }
    if (isset($atts['filter']) && strpos($atts['filter'], '$WPEmail') !== false)
    {
        $useremail = wp_get_current_user()->user_email;
        if ($useremail !== '')

        {
            $atts['filter'] = str_replace('$WPEmail', $useremail, $atts['filter']);
        }
    }
    if (isset($atts['filter']) && strpos($atts['filter'], '$WPUserName') !== false)
    {
        $userlogin = wp_get_current_user()->user_login;
        if ($userlogin !== '')

        {
            $atts['filter'] = str_replace('$WPUserName', $userlogin, $atts['filter']);
        }
    }

    if (isset($atts['filter']) && strpos($atts['filter'], '{!') !== false && strpos($atts['filter'], '}') !== false)
    {

        $d = new sf28c_DynamicURLQuery($atts['filter'], $sf28c_pattern);
        $atts['filter'] = $d->getQuery();

    }

    if (isset($atts['offset']) && strpos($atts['offset'], '{!') !== false && strpos($atts['offset'], '}') !== false)
    {

        $d = new sf28c_DynamicURLQuery($atts['offset'], $sf28c_pattern);
        $atts['offset'] = intval(str_replace(array(
            '\'',
            '"'
        ) , '', $d->getQuery()));

    }

    $s = new sf28c_BuildSOQL($atts);
    $query = $s->getQuery();

    if ($s->valid)
    {

        $atts['fields'] = str_replace(' ', '', $atts['fields']); //remove spaces if entered manually
        $fieldsArray = explode(',', $atts['fields']);
        
        foreach ($fieldsArray as $key => $value) {
            if( preg_match( '/\(([^\)]+)\)/m' , $value, $to_label ) )
            {   
                $fieldsArray[$key] = $to_label[1];
            }

        }

        $l = new sf28c_Transient($atts['o'], $fieldsArray);
        $l->setCurrentLabels();

        $record_session = 'sf28c_records_' . sf28c_Count::get() . '_' . get_the_ID();

        if (!isset($_SESSION[$record_session]) || current_user_can('manage_options') || isset($attr['nocache']))
        {
            $recordsget = new sf28c_Curl();
            $records = $recordsget->getCURL('records', array(
                'query' => $query
            ));
            $_SESSION[$record_session] = $records;

        }

        $records = $_SESSION[$record_session];

        if (isset($atts['debug']))
        {
            $shortcodeout .= '<pre>' . $query . '</pre><br><pre>' . json_encode($atts) . '</pre>';
            $shortcodeout .= '<br><pre>' . json_encode($records) . '</pre>';
            $shortcodeout .= '<br><pre>' . json_encode($l) . '</pre>';
        }

        if (isset($records[0]['message']))
        {
            if (current_user_can('manage_options'))
            {
                if ($records[0]['message'] == 'INVALID_HEADER_TYPE') return 'Re-check the connection settings in the plugin admin page.';
                return 'Looks like there was an error in your query that needs to be fixed  : <br>' . $records[0]['message'];
            }
            else return 'Could not display this information at the moment.';
        }

        if (sf28c_Response::has_compound_fields($fieldsArray)) $formatresponse = sf28c_Response::format($records, 'records_with_compound');
        else $formatresponse = sf28c_Response::format($records, 'records');

        //Calendar Fields
        if (isset($atts['c']) || isset($atts['f']) || isset($atts['p']) || isset($atts['k']))
        {
            $xc = new sf28c_xtraCalendarFields($atts);
            $calendarFields = $xc->setFields();
        }

        $viewRecords = new sf28c_View($formatresponse, $fieldsArray, $l->currentLabelsArray, $calendarFields);

        //Set View
        if (!isset($atts['t']) || ($atts['t'] == 0))
        {
            $forceViewType = 'card';
            $t = $viewRecords->asCards();
        }
        else if ($atts['t'] == 1)
        {
            $forceViewType = 'table';
            $t = $viewRecords->asTables();

        }
        else if ($atts['t'] == 2)
        {
            $forceViewType = 'calendar';
            $t = $viewRecords->asCalendar();
        }
        else if ($atts['t'] == 3)
        {
            $forceViewType = 'json';
            $t = $viewRecords->asJSON();

            if (isset($atts['style']))
            {
                wp_enqueue_style('sforc_main');

                if ($atts['style'] == 'cards')
                {
                    wp_enqueue_style('sforc_cards');
                }

                if ($atts['style'] == 'tables')
                {
                    wp_enqueue_style('sforc_table');
                    wp_enqueue_script('sforc_table_js');
                }

                if ($atts['style'] == 'calendar')
                {
                    wp_enqueue_style('sforc_calendar');
                    wp_enqueue_script('sforc_moment_js');
                    wp_enqueue_script('sforc_calendar_js');
                }

            }

            return $t;
        }

        //Search
        $f = new sf28c_AddFilter($fieldsArray, $l->currentLabelsArray);

        if ((isset($atts['search']) && $atts['search'] == "off") || $forceViewType == 'calendar' || $forceViewType == 'json')
        {
            $searchBox = null;
        }
        else
        {
            $searchBox = $f->getFilterBox();
        }

        //JavaScript Includes
        if ($forceViewType != 'calendar')
        {
            wp_enqueue_script('sforc_list_js');

            $sf_pages = null;

            if (isset($atts['page']) && is_numeric($atts['page'])) $sf_pages = $atts['page'];

            $searchScript = $f->getScript($sf_pages);

            if (function_exists('wp_add_inline_script')) //  WordPress 4.5 >
            wp_add_inline_script('sforc_list_js', $searchScript, 'after');
            else $shortcodeout .= '<script>' . $searchScript . '</script>';
        }
        else if ($forceViewType == 'calendar')
        {
            if (function_exists('wp_add_inline_script')) //  WordPress 4.5 >
            wp_add_inline_script('sforc_calendar_js', $viewRecords->AsCalendarAddScript() , 'after');
            else $shortcodeout .= '<script>' . $viewRecords->AsCalendarAddScript() . '</script>';
        }

        $sforc_pagination = '';

        if (isset($atts['page'])) $sforc_pagination = '<ul class="pagination"></ul>
   			<style type="text/css">
   			.pagination li { 
   				color:black!important;
   				display:inline;  padding: 16px;
   			}
   			</style>';

        $closediv = '</div>';

        if ($forceViewType == 'card') $closediv .= '</div>';

        $shortcodeout .= '<div class="sforc-wrap-content" id="sforc-wrap-' . sf28c_Count::get() . '">' . PHP_EOL . $searchBox . $t . PHP_EOL .$sforc_pagination . PHP_EOL .$closediv;
        
        return $shortcodeout;
    }
    else return "Required fields weren't set in the code to retrieve info, try generating a new code from the Add Cards & Tables Page";

}

function sforc_shortcode_section($atts, $content = null)
{

    $shortcodeout = '';
    $sf28c_pattern = '/(?<={!).*?(?=})/';

    preg_match_all($sf28c_pattern, $content, $matches, PREG_SET_ORDER);

    $sf28c_fields = array();
    $sf28c_enclosedfields = array();
    $replacements = array();

    foreach ($matches as $match)
    {

        array_push($sf28c_fields, $match[0]);
        array_push($sf28c_enclosedfields, '/{!' . $match[0] . '}/');

    }

    if (!isset($atts['n'])) $atts['n'] = 1;

    if (!isset($atts['by'])) $atts['by'] = 'Id';

    if (isset($atts['style']))
    {
        wp_enqueue_style('sforc_main');

        if ($atts['style'] == 'cards')
        {
            wp_enqueue_style('sforc_cards');
        }

        if ($atts['style'] == 'tables')
        {
            wp_enqueue_style('sforc_table');
            wp_enqueue_script('sforc_table_js');
        }

        if ($atts['style'] == 'calendar')
        {
            wp_enqueue_style('sforc_calendar');
            wp_enqueue_script('sforc_moment_js');
            wp_enqueue_script('sforc_calendar_js');
        }

    }

    $atts['fields'] = implode(',', array_unique($sf28c_fields));

    sf28c_Count::set();

    if (isset($atts['filter']) && strpos($atts['filter'], '$WPId') !== false)
    {
        $userid = get_current_user_id();
        if ($userid === 0) return "Please login to view this info";
        else $atts['filter'] = str_replace('$WPId', $userid, $atts['filter']);
    }
    if (isset($atts['filter']) && strpos($atts['filter'], '$WPEmail') !== false)
    {
        $useremail = wp_get_current_user()->user_email;
        if ($useremail !== '')
        {
            $atts['filter'] = str_replace('$WPEmail', $useremail, $atts['filter']);
        }
    }
    if (isset($atts['filter']) && strpos($atts['filter'], '$WPUserName') !== false)
    {
        $userlogin = wp_get_current_user()->user_login;
        if ($userlogin !== '')

        {
            $atts['filter'] = str_replace('$WPUserName', $userlogin, $atts['filter']);
        }
    }

    if (isset($atts['filter']) && strpos($atts['filter'], '{!') !== false && strpos($atts['filter'], '}') !== false)
    {

        $d = new sf28c_DynamicURLQuery($atts['filter'], $sf28c_pattern);
        $atts['filter'] = $d->getQuery();

    }

    if (isset($atts['offset']) && strpos($atts['offset'], '{!') !== false && strpos($atts['offset'], '}') !== false)
    {

        $d = new sf28c_DynamicURLQuery($atts['offset'], $sf28c_pattern);
        $atts['offset'] = intval(str_replace(array(
            '\'',
            '"'
        ) , '', $d->getQuery()));

    }

    $s = new sf28c_BuildSOQL($atts);
    $query = $s->getQuery();

    if ($s->valid)
    {
        $record_session = 'sf28c_records_' . sf28c_Count::get() . '_' . get_the_ID();

        if (!isset($_SESSION[$record_session]) || current_user_can('manage_options') || isset($attr['nocache']))
        {
            $recordsget = new sf28c_Curl();
            $records = $recordsget->getCURL('records', array(
                'query' => $query
            ));
            $_SESSION[$record_session] = $records;
        }

        $records = $_SESSION[$record_session];

        if (isset($atts['debug']))
        {
            $shortcodeout .= '<pre>' . $query . '</pre><br><pre>' . json_encode($atts) . '</pre>';
            $shortcodeout .= '<br><pre>' . json_encode($records) . '</pre>';
        }

        if (isset($records['records'][0])) //Valid REST
        
        {

            $record = $records['records'];

            foreach ($record as $rkey => $r)
            {

                foreach ($sf28c_fields as $key => $value)
                {

                    if (array_key_exists($value, $r)) array_push($replacements, $r[$value]);

                }

                $shortcodeout .= preg_replace($sf28c_enclosedfields, $replacements, $content);

                $replacements = array();

            }

        }

        if (isset($records[0]['message']))
        {
            if (current_user_can('manage_options'))
            {
                if ($records[0]['message'] == 'INVALID_HEADER_TYPE') return 'Re-check the connection settings in the plugin admin page.';
                return 'Looks like there was an error in your query that needs to be fixed  : <br>' . $records[0]['message'];
            }
            else return 'Could not display this information at the moment.';
        }
    }
    else return "Required fields weren't set in the section code to retrieve info. Have you set the Object Name? eg:  o='Opportunity'";

    return $shortcodeout;

}

