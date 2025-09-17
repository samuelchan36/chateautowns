var masterCSS = 'https://use.typekit.net/zpj1wza.css';
var tinyFonts = "Neue Haas=\"neue-haas-unica\", Helvetica Neue Roman=\"HelveticaNeueRoman\", Helvetica Neue Bold=\"HelveticaNeueBold\"";
var tinySizes = "12px 14px 16px 18px 22px 24px 30px 36px 48px 60px 100px";
var blockFormats = "Header 1=h1;Header 2=h2;Header 3=h3;Header 4=h4;Paragraph=p;Container=div";

var sectionWidths = [ ["masthead", "Masthead (full width, no margins)"], ["max", "Full width"], ["full", "Extended width (larger than standard)"], ["short", "Shorter width (shorter than standard)"], ["full-screen", "Full screen (home page)"] ];


var tinyStyles = [
     {title: 'Button Holder', selector: 'div', classes: 'buttons'}
    , {title: 'Standard Button', selector: 'a', classes: 'btn2'}
    , {title: 'Alternative Button', selector: 'a', classes: 'btn'}
    , {title: 'Auto Scroll', selector: 'a', classes: 'scrolling'}

	, {title: 'Larger Text', selector: 'p', classes: 'larger'}
	, {title: 'Short Width Text', selector: 'p', classes: 'short'}
	, {title: 'Shorter Width Text', selector: 'p', classes: 'shorter'}
	, {title: 'Shortest Width Text', selector: 'p', classes: 'shortest'}
	, {title: 'Two Columns Text', selector: 'p', classes: 'two-columns'}

	
	, {title: 'Two Columns List', selector: 'ul', classes: 'two-columns'}

	, {title: 'Black Text', selector: '', classes: 'c-black'}
    , {title: 'White Text', selector: '', classes: 'c-white'}
    , {title: 'Red Text', selector: '', classes: 'c-primary'}
    , {title: 'Red Background', selector: '', classes: 'b-primary'}
    , {title: 'Black Background', selector: '', classes: 'b-black'}
    
	, {title: 'Full Width Image', selector: 'img', classes: 'full'}
    
	, {title: 'Caption', selector: 'div', classes: 'caption'}
    , {title: 'Caption - Left Centered', selector: 'div', classes: 'left'}
    , {title: 'Caption - Right Centered', selector: 'div', classes: 'right'}
    , {title: 'Caption - Horizontally Centered', selector: 'div', classes: 'center'}
    , {title: 'Caption - Vertically Centered', selector: 'div', classes: 'middle'}
    , {title: 'Caption - Top Centered', selector: 'div', classes: 'top'}
    , {title: 'Caption - Bottom Centered', selector: 'div', classes: 'bottom'}

	, {title: 'Caption - Masthead', selector: 'div', classes: 'caption-masthead'}
    
	, {title: 'Left align content', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'left'}
    , {title: 'Center align content', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'center'}
    , {title: 'Right align content', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'right'}
    , {title: 'Left align block', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'align-left'}
    , {title: 'Center align block', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'align-center'}
    , {title: 'Right align block', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'align-right'}
    , {title: 'Margin top 150px', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'margin-top-150'}
    , {title: 'Margin top 100px', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'margin-top-100'}
    , {title: 'Margin top 50px', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'margin-top-50'}
    , {title: 'Margin top 0px', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'margin-top-0'}
    , {title: 'Margin bottom 150px', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'margin-bottom-150'}
    , {title: 'Margin bottom 100px', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'margin-bottom-100'}
    , {title: 'Margin bottom 50px', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'margin-bottom-50'}
    , {title: 'Margin bottom 0px', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'margin-bottom-0'}
    , {title: 'Left/right padding (20px)', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'padding'}
    , {title: 'Left/right padding (50px)', selector: "p,img,div,h1,h2,h3,h4,h5,h6", classes: 'extra-padding'}
  ];


var tinyTemplates = [
					    {title: 'Masthead Image with Headline', description: 'Typical Masthead (with separate mobile image)', url: '/cms/app/components/content.html'},
					    {title: 'Masthead Image with Headline', description: 'Typical Masthead (same image for desktop and mobile)', url: '/cms/app/components/content2.html'},
					    {title: 'Basic Text Block with Headlines', description: 'Basic text component', url: '/cms/app/components/content3.html'},
					    {title: 'Gray Block', description: 'Gray block with rounded corners', url: '/cms/app/components/content4.html'},
					    {title: 'Grid with 3 columns', description: '', url: '/cms/app/components/content5.html'},
					    {title: 'Resources list', description: '', url: '/cms/app/components/content6.html'},
					    {title: 'Accordion style', description: '', url: '/cms/app/components/content7.html'}
					  ];
