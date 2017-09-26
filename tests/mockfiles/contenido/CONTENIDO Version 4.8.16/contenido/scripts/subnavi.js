/**
 * Subnavigation. Handles tab highlighting.
 * Replaces most of the rowMark.js functionality.
 *
 * @author dirk.eschler
 */
var sub = {

    /**
     * Highlights the first tab by default (div#navcontainer li#c_0 a)
     * by setting the class to 'current'. Is called in subnavigation template.
     */
    init: function() {
        var anchors = this._getAnchors();
        if (anchors[0]) {
            anchors[0].className = 'current';
        }
    },

    /**
     * Highlights the active tab.
     *
     * @param {Object} cElm Clicked a-element, resp. the tab to highlight.
     * @todo Consider new name ("highlight"?) and rename remaining instances.
     */
    clicked: function(cElm) {
        var anchors = this._getAnchors(),
            i;
        for (i=0; i<anchors.length; i++) {
            if (anchors[i] === cElm) {
                anchors[i].className = 'current';
            } else {
                anchors[i].className = '';
            }
        }
    },

    /**
     * Highlights a tab by its element id. Useful for highlighting from an outer frame.
     *
     * @param {String} id Element id of tab to highlight
     * @param {Object} frame Reference to frame hodling the subnavigation:
     *                       top.content.right.right_top (when there is a left/right frameset)
     *                       top.content.right_top       (when there is no left/right frameset)
     */
    highlightById: function(id, frame) {
        this._reset(frame);
        var elem = this._getAnchorById(id, frame);
        if (elem) {
            elem.className = 'current';
        }
    },

    /**
     * Returns list of all found anchors within sub navigation
     * @param {Object} [frame] Optional, reference to frame handling the sub navigation
     * @return {Array}  List of found HTMLElement
     * @protected
     */
    _getAnchors: function(frame) {
        var obj = (frame) ? frame.document : document;
        try {
            var list = obj.getElementById("navlist").getElementsByTagName("a");
            return list;
        } catch (e) {
            return [];
        }
    },

    /**
     * Returns anchor element by it's id
     * @param {String} id
     * @param {Object} [frame] Optional, reference to frame handling the sub navigation
     * @return {HTMLElement|null}
     * @protected
     */
    _getAnchorById: function(id, frame) {
        var obj = (frame) ? frame.document : document;
        try {
            var elem = obj.getElementById(id).getElementsByTagName('a')[0];
            return elem;
        } catch (e) {
            return null;
        }
    },

    /**
     * Reset all tabs.
     * @param {Object} [frame] Optional, reference to frame handling the sub navigation
     * @protected
     */
    _reset: function(frame) {
        frame = frame || null;
        var anchors = this._getAnchors(frame),
            i;
        for (i=0; i<anchors.length; i++) {
            anchors[i].className = '';
        }
    },

    /*
    unhighlight: function() {
        var ul = this.frame.document.getElementById('navlist'),
            as = ul.getElementsByTagName('a'), i;
        for (i=0; i<=as.length; i++) {
            if (as[i]) {
                as[i].className = '';
            }
        }
    },*/

    /**
     * Dummy method to avoid breakage.
     *
     * @todo Locate remaining inline calls to sub.click() and remove them
     */
    'click': function() {
            //console.log("remove me");
        return;
    }
};