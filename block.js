/*
tminus/countdown
version: 0.1
*/

const {registerBlockType} = wp.blocks; //Blocks API
const {__} = wp.i18n; //translation functions
const {RichText,InnerBlocks,InspectorControls} = wp.editor;
const {PanelBody,TextControl,SelectControl,ToggleControl,DateTimePicker,ServerSideRender} = wp.components;
const el = wp.element.createElement;

// tminus icon full
const iconEl = el('svg', { width: 20, height: 20 },
  el('path', { fill: "#1B2B3A", d: "M10,17.635c3.514-0.02,6.355-2.873,6.355-6.391c0-3.518-2.842-6.372-6.355-6.391V0.206h1.671v1.943H10.7V2.8,c1.403,0.114,2.708,0.569,3.836,1.28l1.368-1.369l1.539,1.539l-1.223,1.224c1.42,1.521,2.29,3.563,2.29,5.808,c0,4.7-3.812,8.512-8.512,8.512L10,17.635z" } ),
  el('path', { fill: "#394E66", d: "M10.001,19.794c-4.7,0-8.512-3.812-8.512-8.512c0-2.245,0.87-4.287,2.29-5.808L2.557,4.25l1.539-1.539,L5.464,4.08C6.592,3.369,7.896,2.914,9.3,2.8V2.149H8.329V0.206H10v4.647c-3.514,0.019-6.355,2.873-6.355,6.391,c0,3.518,2.842,6.371,6.355,6.391L10.001,19.794z" } ),
  el('path', { fill: "#BCC6DD", d: "M10,5.834c-3.046,0-5.428,2.451-5.428,5.498h2.27c0-1.792,1.367-3.193,3.159-3.193V5.834z" } ),
  el('rect', {x: "8.161", y: "6.937", transform: "matrix(-0.7065 0.7077 -0.7077 -0.7065 21.6508 10.6018)", fill: "#394E66", width: "1.292", height:"5.167"} )
);

// tminus icon corner
/*
const iconEl = el('svg', { width: 20, height: 20 },
  el('path', { fill: "#2D4466", d: "M7.276,19.533c0-6.713,5.424-12.224,12.127-12.26V0.438h-2.881v3.35h1.674V4.91,c-2.417,0.197-4.669,0.979-6.614,2.207l-2.359-2.36L6.569,7.41l2.109,2.11c-2.449,2.621-3.949,6.143-3.949,10.014" } ),
  el('path', { fill: "#CDD2E1", d: "M19.403,8.948c-5.912,0-10.537,4.673-10.537,10.585h4.407c0-3.479,2.652-6.113,6.13-6.113V8.948z" } ),
  el('polygon', { fill: "#2D4466", points: "17.9,19.533 13.195,14.828 14.698,13.325 19.403,18.03 19.403,19.533" } )
);
*/

//console.log(tminus_options);

registerBlockType( 'tminus/countdown', {
	title: __( 'T(-) Countdown' ),
  description: __( 'Juicy description of T(-) Countdown that makes you just want to use it.', 't-countdown' ),
	category:  __( 'common' ),
  keywords: [
  		__( 'countdown', 't-countdown' ),
  		__( 'timer', 't-countdown' ),
  		__( 'tminus', 't-countdown' ),
  	],
	icon: iconEl,
	attributes:  {
		id: {
			type: 'string',
		},
    style: {
			type: 'string',
      default: 'jedi'
		},
    t: {
      type: 'number',
      default: 60 * (1440 + Math.ceil(Date.now() / 60000)) // 24 hours from Date.now
		},
    secs: {
			type: 'string',
      default: 0
		},
    omityears: {
      type: 'boolean',
      default: (tminus_options.omityears == 'true')
    },
    omitmonths: {
      type: 'boolean',
      default: (tminus_options.omitmonths == 'true')
    },
    omitweeks: {
      type: 'boolean',
      default: (tminus_options.omitweeks == 'true')
    },
    yearlabel: {
			type: 'string',
      default: tminus_options.yearlabel
		},
    monthlabel: {
			type: 'string',
      default: tminus_options.monthlabel
		},
    weeklabel: {
			type: 'string',
      default: tminus_options.weeklabel
		},
    daylabel: {
			type: 'string',
      default: tminus_options.daylabel
		},
    hourlabel: {
			type: 'string',
      default: tminus_options.hourlabel
		},
    minutelabel: {
			type: 'string',
      default: tminus_options.minutelabel
		},
    secondlabel: {
			type: 'string',
      default: tminus_options.secondlabel
		}
	},

  edit(props){
		const attributes =  props.attributes;
		const setAttributes =  props.setAttributes;

		//Functions to update attributes
    function changeId(id){
			setAttributes({id});
		}

    function changeStyle(style){
			setAttributes({style});
		}

    function changeDate(t){
      t = Math.floor( Date.parse(t) / 1000 );
			setAttributes({t});
		}

    function changeSecs(secs){
      if(secs < 0){
        secs = 0;
      }
      if(secs > 59){
        secs = 59;
      }
			setAttributes({secs});
		}

    function changeOmitYears(omityears){
			setAttributes({omityears});
		}

    function changeOmitMonths(omitmonths){
			setAttributes({omitmonths});
		}

    function changeOmitWeeks(omitweeks){
			setAttributes({omitweeks});
		}

    function changeYearLabel(yearlabel){
			setAttributes({yearlabel});
		}

    function changeMonthLabel(monthlabel){
			setAttributes({monthlabel});
		}

    function changeWeekLabel(weeklabel){
			setAttributes({weeklabel});
		}

    function changeDayLabel(daylabel){
			setAttributes({daylabel});
		}

    function changeHourLabel(hourlabel){
			setAttributes({hourlabel});
		}

    function changeMinuteLabel(minutelabel){
			setAttributes({minutelabel});
		}

    function changeSecondLabel(secondlabel){
			setAttributes({secondlabel});
		}

		//Display block preview and UI
		return el('div', {}, [

      //el('div', {}, 'This is a T(-) Countdown placeholder from block edit callback' ),
      //Preview a block with a PHP render callback
      el( ServerSideRender, {
				block: 'tminus/countdown',
        attributes: attributes
			} ),

			//Block Inspector
			el( InspectorControls, {},
				[

          el(PanelBody, {
              title: __('Countdown ID & Style'),
              initialOpen: true,
          },
              [
                el(TextControl, {
                  label: __( 'Countdown ID' ),
                  value: attributes.id,
                  onChange: changeId,
                }),

                el(SelectControl, {
                  label: __( 'Countdown Style' ),
                  value: attributes.style,
                  onChange: changeStyle,
                  options: tminus_options.styles
                }),
              ]
          ),

          el(PanelBody, {
              title: __('Launch Date & Time'),
              initialOpen: true,
          },
              [
                el(DateTimePicker, {
                  //currentDate: attributes.t,
                  currentDate: attributes.t * 1000,
                  onChange: changeDate
                }),
                el(TextControl, {
                  label: __( 'Seconds' ),
                  value: attributes.secs,
                  onChange: changeSecs,
                  type: 'number',
                  min: 0,
                  max: 59,
                  help: __( 'Currently, seconds need to be entered seperatly due to the fact that the Gutenberg Time Picker incrediously does not support seconds.' )
                }),
              ]
          ),

          el(PanelBody, {
              title: __('Date Unit Display'),
              initialOpen: false,
          },
              [
                el(ToggleControl, {
                  checked: attributes.omityears,
                  label: __( 'Ommit Years' ),
                  onChange: changeOmitYears,
                }),

                el(ToggleControl, {
                  checked: attributes.omitmonths,
                  label: __( 'Ommit Months' ),
                  onChange: changeOmitMonths,
                }),

                el(ToggleControl, {
                  checked: attributes.omitweeks,
                  label: __( 'Ommit Weeks' ),
                  onChange: changeOmitWeeks,
                }),

              ]
          ),

          el(PanelBody, {
              title: __('Date & Time Labels'),
              initialOpen: false,
          },
              [
                el(TextControl, {
                  value: attributes.yearlabel,
                  label: __( 'Years Label' ),
                  onChange: changeYearLabel,
                }),

                el(TextControl, {
                  value: attributes.monthlabel,
                  label: __( 'Months Label' ),
                  onChange: changeMonthLabel,
                }),

                el(TextControl, {
                  value: attributes.weeklabel,
                  label: __( 'Weeks Label' ),
                  onChange: changeWeekLabel,
                }),

                el(TextControl, {
                  value: attributes.daylabel,
                  label: __( 'Days Label' ),
                  onChange: changeDayLabel,
                }),

                el(TextControl, {
                  value: attributes.hourlabel,
                  label: __( 'Hours Label' ),
                  onChange: changeHourLabel,
                }),

                el(TextControl, {
                  value: attributes.minutelabel,
                  label: __( 'Mintues Label' ),
                  onChange: changeMinuteLabel,
                }),

                el(TextControl, {
                  value: attributes.secondlabel,
                  label: __( 'Seconds Label' ),
                  onChange: changeSecondLabel,
                }),
              ]
          ),

				]
			)
		] )
	},

  save: function( props ) {
      var attributes = props.attributes;

      if(!attributes.id){
          var S4 = function() {
             return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
          };
          attributes.id = 'id' + (S4()+S4()+S4()+S4()+S4());
      }

			return (
				    el( 'div', { } )
      );
   }
});
