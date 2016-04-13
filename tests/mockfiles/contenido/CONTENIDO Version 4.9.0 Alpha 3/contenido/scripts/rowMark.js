/**
 * Table rowMark
 *
 * myRow = new rowMark(1,2,3,4)
 *
 *   1:    Farbe des Over Effekts z.B. "#ff0000" - string
 *   2:    Farbe des Mark Effeks - string
 *   3:    Farbe des Over Effeks bei der Marked Row - string
 *   4: Funktion die bei onClick aufgerufen wird - string
 *
 *   <tr class="grau" onMouseOver="myRow.over(this)" onMouseOut="myRow.out(this)" onClick="myRow.click(this)">
 *       <td>eine Zeile</td>
 *       <td><img src="einbild.gif"></td>
 *   </tr>
 *
 * @param String sOverColor     Over-Color
 * @param String sMarkedColor   Marked-Color
 * @param String sOverMarked    Over-Marked-Color
 *
 * @author Jan Lengowski <Jan.Lengowski@4fb.de>
 * @modified Timo Trautmann <timo.trautmann@4fb.de> added support for second marked Color used as active sync color, defined by class
 variable markedSyncColor Each table row containing class name con_sync gets this marked color
 * @version 1.2
 * @copyright Jan Lengowski 2002
 */
function rowMark(overColor, markedColor, overMarked, onClick, instanceName) {

    /**
     * Set class properties
     * @access private
     */
    this.instanceName = instanceName;
    this.overColor = overColor;
    this.markedColor = markedColor;
    this.overMarked = overMarked;
    this.onClick = onClick;
    this.highlightFrame = "#CCCCCC";
    this.backgroundColor = "#E2E2E2";
    this.markedSyncColor = "#AED2F1";
    this.syncColor = "#ddecf9";

    /**
     * dynamic properties
     * @access private
     */
    this.oldColor = '';
    this.oldColorMarked = '';
    this.markedRow = '';

    /**
     * Define class methods
     * @access private
     */
    this.over = rowMark_over;
    this.out = rowMark_out;
    this.click = rowMark_click;
    this.reset = rowMark_reset;

    /**
     * Browsercheck
     * @access private
     */
    this.browser = '';

}

/**
 * rowMark::over()
 * @param object oRow table row object
 */
function rowMark_over(oRow) {
    if (oRow == null)
    {
        return;
    }

    if ( oRow.style.backgroundColor != this.markedColor ) {
        this.oldColor = oRow.style.backgroundColor;
    }

    oRow.style.backgroundColor = this.overColor;

    /*if ( oRow.style.backgroundColor == this.markedColor ) {
     oRow.style.backgroundColor = this.overMarked;
     } else {
     oRow.style.backgroundColor = this.overColor;
     }*/

}

/**
 * rowMark::out()
 * @param object oRow table row object
 */
function rowMark_out(oRow) {

    if (oRow == this.markedRow) {
        if (oRow.className=="con_sync") {
            oRow.style.backgroundColor = this.markedSyncColor;
        } else {
            oRow.style.backgroundColor = this.markedColor;
        }
    } else {
        oRow.style.backgroundColor = this.oldColor;
    }

}

function rowMark_reset () {
    var oObjects = document.getElementsByTagName('tr');
    var pattern=eval("/" + this.instanceName + "\\.click\\(this\\)/m");

    for (var i = 0; i < oObjects.length; i++) {
        var sOnclick = String(oObjects[i].onclick);
        if (sOnclick != '' && sOnclick != 'undefined') {
            if (sOnclick.match(pattern)) {
                if (oObjects[i].className=="con_sync") {
                    oObjects[i].style.backgroundColor = this.syncColor;
                } else {
                    oObjects[i].style.backgroundColor = '#FFFFFF';
                }
            }
        }
    }
    this.markedRow = '';
}

/**
 * rowMark::over()
 * @param object oRow table row object
 */
function rowMark_click(oRow)
{
    if (oRow == null)
    {
        return;
    }
    if (typeof this.markedRow != "object")
    {
        if (oRow.className=="con_sync") {
            oRow.style.backgroundColor = this.markedSyncColor;
        } else {
            oRow.style.backgroundColor = this.markedColor;
        }

        this.markedRow = oRow;
        this.oldColorMarked = this.oldColor;
        if ( this.onClick != "")
        {
            eval( this.onClick );
        }
    }
    else if (this.markedRow != oRow)
    {
        /* reset old */
        this.markedRow.style.backgroundColor = this.oldColorMarked;
        /* highlight new*/
        if (oRow.className=="con_sync") {
            oRow.style.backgroundColor = this.markedSyncColor;
        } else {
            oRow.style.backgroundColor = this.markedColor;
        }

        this.markedRow = oRow;
        this.oldColorMarked = this.oldColor;

        if ( this.onClick != "")
        {
            eval(this.onClick);
        }
    }
}


/**
 * Table rowMark with image rollover
 *
 * REQUIRES rowMark CLASS!
 *
 * myRow = new imgMark(1, 2, 3, 4, 5, 6);
 *
 *  1:  Farbe des Over Effekts z.B. "#ff0000" - string
 *  2:    Farbe des Mark Effeks - string
 *  3:    Farbe des Over Effeks bei der Marked Row - string
 *  4:  Pfad des Bildes das bei .over() gewechselt wird - string
 *  5:  Pfad des Bildes das bei .out() gewechselt wird - string
 *  6:  Function die bei onClick aufgerufen wird - string
 *
 *   <tr class="grau" onMouseOver="myRow.over(this, 0)" onMouseOut="myRow.out(this, 0)" onClick="myRow.click(this)">
 *       <td>eine Zeile</td>
 *       <td><img src="einbild.gif"></td>
 *   </tr>
 *
 * @author Jan Lengowski <Jan.Lengowski@4fb.de>
 * @version 1.2
 * @copyright Jan Lengowski 2002
 */
function imgMark(overColor, markedColor, overMarked, imgOutSrc, imgOverSrc, onClick) {

    /**
     * Call parent class constructor
     * @access private
     */
    this.base = rowMark;
    this.base(overColor, markedColor, overMarked, onClick);

    /**
     * Set image path properties
     * @access private
     */
    this.imgOutSrc = imgOutSrc;
    this.imgOverSrc = imgOverSrc;

    /**
     * Modify inherited .over() method
     * @access private
     */
    var str = this.over + '';
    var astr = str.split('\n');
    var fstr = 'var img = oRow.getElementsByTagName("IMG"); img[imgId].src = this.imgOverSrc;';
    for (i=2; i<astr.length-2; i++) {
        fstr += astr[i];
    }
    this.over = new Function ('oRow', 'imgId', fstr);

    /**
     * Modify inherited .out() method
     * @access private
     */
    var str = this.out + '';
    var astr = str.split('\n');
    var fstr = 'var img = oRow.getElementsByTagName("IMG");img[imgId].src = this.imgOutSrc;';

    for (i=2; i<astr.length-2; i++) {
        fstr += astr[i];
    }
    this.out = new Function ('oRow', 'imgId', fstr);

}
imgMark.prototype = new rowMark;

/* Sets the path value
 in the area 'upl' */
function setPath( obj ) {
    parent.parent.frames["left"].frames['left_top'].document.forms[1].path.value = obj.id;
    parent.parent.frames["left"].frames['left_top'].document.getElementById("caption2").innerHTML = obj.id;
}

/**
 * Function for showing and hiding synchronsation options
 *
 * @param boolean permSyncCat true shows options / flase hides options
 *
 * @author Timo Trautmann <timo.trautmann@4fb.de>
 * @copyright four for business AG <www.4fb.de>
 */
function refreshSyncScreen(permSyncCat) {
    //curLanguageSync = syncFrom;
    var frame = parent.parent.frames["left"].frames['left_top'];
    var syncElem = frame.document.getElementById('sync_cat_single');
    var syncElemMultiple = frame.document.getElementById('sync_cat_multiple');
    if (syncElem && syncElemMultiple) {
        if (permSyncCat == 0) {
            syncElem.style.display = 'none';
            syncElemMultiple.style.display = 'none';
        } else {
            syncElem.style.display = 'block';
            syncElemMultiple.style.display = 'block';
        }
        parent.parent.frameResize.resizeTopLeftFrame(frame.document.getElementById('top_left_container').offsetHeight+1);
    }
}

/**
 * Interface function for transfering
 * data from left-bottom frame to the
 * configuration object in the left-top
 * frame.
 *
 * @param object HTML Table Row Object
 *
 * @author Jan Lengowski <Jan.Lengowski@4fb.de>
 * @copyright four for business AG <www.4fb.de>
 */
function conInjectData(obj) {
    /* Configuration Object Reference */
    cfgObj = parent.parent.frames["left"].frames['left_top'].cfg;

    /* Split the data string.
     0 -> category id
     1 -> category template id
     2 -> category online
     3 -> category public
     4 -> has right for: template
     5 -> has right for: online
     6 -> has right for: public
     7 -> has right for: template_edit
     8 -> cat is syncable
     9 -> idstring not splitted */
    tmp_data = obj.id;
    data = tmp_data.split("-");

    if ( data.length == 9 ) {
        /* Transfer data to the cfg object
         through the .load() method */
        //cfgObj.load(data[0], data[1], data[2], data[3], data[4], data[5], data[6], data[7]);
        cfgObj.load(data[0], data[1], data[2], data[3], data[4], data[5], data[6], data[7], data[8], obj.id);
        refreshSyncScreen(data[8]);
    } else {
        cfgObj.reset();
        refreshSyncScreen(0);
    }

    /* String for debugging */
    str  = "";
    str += "Category ID is: "     + data[0] + "\n";
    str += "Template ID is: "     + data[1] + "\n";
    str += "Online status is: "   + data[2] + "\n";
    str += "Public status is: "   + data[3] + "\n";
    str += "Right for Template: " + data[4] + "\n";
    str += "Right for Online: "   + data[5] + "\n";
    str += "Right for Public: "   + data[6] + "\n";
    str += "Right for Template Config: "   + data[7] + "\n";
    str += "data7: "   + data[7] + "\n";

    if (is.NS)
    {
        if (!parent.parent.frames["left"].frames['left_top'].cfg.scrollX) parent.parent.frames["left"].frames['left_top'].cfg.scrollX = 0;
        if (!parent.parent.frames["left"].frames['left_top'].cfg.scrollY) parent.parent.frames["left"].frames['left_top'].cfg.scrollY = 0;

        parent.parent.frames["left"].frames['left_top'].cfg.scrollX = scrollX;
        parent.parent.frames["left"].frames['left_top'].cfg.scrollY = scrollY;
    }
}

/**
 rowMark instances
 **/

/* rowMark instance for the
 general use */
row = new rowMark('#f9fbdd', '#ecf1b2', '#cccccc', 'row');

/* rowMark instance for the
 Subnavigation */
sub = new rowMark('red', '#FFF', 'blue', 'sub');

/* rowMark instance for the
 Content area */
con = new rowMark('#f9fbdd', '#ecf1b2', '#a9aec2', 'conInjectData(oRow)', 'con');

/* rowMark instance for the
 Content Category area */
str = new rowMark('#f9fbdd', '#ecf1b2', '#a9aec2', 'refreshSelectedBaseCategory(oRow)', 'str');

/* rowMark instance for the
 Upload area */
//upl = new rowMark('#f9fbdd', '#ecf1b2', '#a9aec2', 'setPath(oRow)', 'upl');
upl = new rowMark('#f9fbdd', '#ecf1b2', '#a9aec2', 'refreshPathFrame(oRow)', 'upl');

/* Create a new rowMark
 Instance for the Content-
 Article overview area */
artRow = new rowMark('#f9fbdd', '#ecf1b2', '#ecf1b2', 'if (conArtOverviewExtractData(oRow) == false) { window.setTimeout(conArtOverviewExtractData(oRow), 250); }', 'artRow');


/* rowMark instance for
 area 'lay' */
lay = new rowMark('#f9fbdd', '#ecf1b2', '#a9aec2', 'saveObj(oRow)', 'lay');

function saveObj(oRow)
{
    parent.parent.frames["left"].frames["left_top"].obj = oRow.id;
}

function refreshPathFrame(oRow) {
    var newPath = oRow.id;
    var left_top = parent.parent.frames["left"].frames["left_top"];

    if (left_top) {
        if (left_top.document.getElementById('caption2')) {
            left_top.document.getElementById('caption2').innerHTML = newPath;
        }

        if (left_top.document.newdir) {
            left_top.document.newdir.path.value = newPath;
        }

        id_path = newPath;
    }
}

/**refreshSelectedBaseCategory
 * Generic function to reMark a row
 */
function reMark(sObjId)
{
    var elm = document.getElementById(sObjId);

    if (typeof elm == 'object')
    {
        lay.over(elm);
        lay.click(elm);

        if (elm && elm != null)
        {
            elm.scrollIntoView(false);
        }
    }
}

/**
 * Function returns offset left, top, width and heigth of a given htnmlelement as array
 *
 * @param object oElement - Object which should be analyzed
 * @return array - containing dimension information
 * @deprecated  Use jQuery .position()
 * @fixme  Redundant code, see str_overview.js
 */
function getElementPostion(oElement) {
    var iHeigth = oElement.offsetHeight,
        iWidth = oElement.offsetWidth,
        iTop = 0, iLeft = 0;
    while (oElement) {
        iTop += oElement.offsetTop || 0;
        iLeft += oElement.offsetLeft || 0;
        oElement = oElement.offsetParent;
    };
    return [iLeft, iTop, iHeigth, iWidth];
}