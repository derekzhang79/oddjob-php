/* Moment.js | version : 1.1.2 | author : Tim Wood | license : MIT */
(function(a,b){function k(a,b){var c=a+"";while(c.length<b)c="0"+c;return c}function l(b,c,d,e){var f=typeof c=="string",g=f?{}:c,h,i,j;return f&&e&&(g[c]=e),h=(g.ms||g.milliseconds||0)+(g.s||g.seconds||0)*1e3+(g.m||g.minutes||0)*6e4+(g.h||g.hours||0)*36e5+(g.d||g.days||0)*864e5+(g.w||g.weeks||0)*6048e5,i=(g.M||g.months||0)+(g.y||g.years||0)*12,h&&b.setTime(+b+h*d),i&&(j=b.getDate(),b.setDate(1),b.setMonth(b.getMonth()+i*d),b.setDate(Math.min((new a(b.getFullYear(),b.getMonth()+1,0)).getDate(),j))),b}function m(a){return Object.prototype.toString.call(a)==="[object Array]"}function n(b){return new a(b[0],b[1]||0,b[2]||1,b[3]||0,b[4]||0,b[5]||0,b[6]||0)}function o(b,d){function s(d){var m,t;switch(d){case"M":return e+1;case"Mo":return e+1+q(e+1);case"MM":return k(e+1,2);case"MMM":return c.monthsShort[e];case"MMMM":return c.months[e];case"D":return f;case"Do":return f+q(f);case"DD":return k(f,2);case"DDD":return m=new a(g,e,f),t=new a(g,0,1),~~((m-t)/864e5+1.5);case"DDDo":return m=s("DDD"),m+q(m);case"DDDD":return k(s("DDD"),3);case"d":return h;case"do":return h+q(h);case"ddd":return c.weekdaysShort[h];case"dddd":return c.weekdays[h];case"w":return m=new a(g,e,f-h+5),t=new a(m.getFullYear(),0,4),~~((m-t)/864e5/7+1.5);case"wo":return m=s("w"),m+q(m);case"ww":return k(s("w"),2);case"YY":return k(g%100,2);case"YYYY":return g;case"a":return i>11?r.pm:r.am;case"A":return i>11?r.PM:r.AM;case"H":return i;case"HH":return k(i,2);case"h":return i%12||12;case"hh":return k(i%12||12,2);case"m":return j;case"mm":return k(j,2);case"s":return l;case"ss":return k(l,2);case"zz":case"z":return(b.toString().match(p)||[""])[0].replace(n,"");case"L":case"LL":case"LLL":case"LLLL":return o(b,c.longDateFormat[d]);default:return d.replace("\\","")}}var e=b.getMonth(),f=b.getDate(),g=b.getFullYear(),h=b.getDay(),i=b.getHours(),j=b.getMinutes(),l=b.getSeconds(),m=/(\\)?(Mo|MM?M?M?|Do|DDDo|DD?D?D?|dddd?|do?|w[o|w]?|YYYY|YY|a|A|hh?|HH?|mm?|ss?|zz?|LL?L?L?)/g,n=/[^A-Z]/g,p=/\([A-Za-z ]+\)|:[0-9]{2} [A-Z]{3} /g,q=c.ordinal,r=c.meridiem;return d.replace(m,s)}function p(a,b){function j(a,b){switch(a){case"M":case"MM":c[1]=~~b-1;break;case"D":case"DD":case"DDD":case"DDDD":c[2]=~~b;break;case"YY":b=~~b,c[0]=b+(b>70?1900:2e3);break;case"YYYY":c[0]=~~b;break;case"a":case"A":i=b.toLowerCase()==="pm";break;case"H":case"HH":case"h":case"hh":c[3]=~~b;break;case"m":case"mm":c[4]=~~b;break;case"s":case"ss":c[5]=~~b}}var c=[0],d=/(\\)?(MM?|DD?D?D?|YYYY|YY|a|A|hh?|HH?|mm?|ss?)/g,e=/(\\)?([0-9]+|am|pm)/gi,f=a.match(e),g=b.match(d),h,i;for(h=0;h<g.length;h++)j(g[h],f[h]);return i&&c[3]<12&&(c[3]+=12),n(c)}function q(a,b){var c=Math.min(a.length,b.length),d=Math.abs(a.length-b.length),e=0,f;for(f=0;f<c;f++)~~a[f]!==~~b[f]&&e++;return e+d}function r(a,b){var c,d=/(\\)?([0-9]+|am|pm)/gi,e=a.match(d),f=[],g=99,h,i,j;for(h=0;h<b.length;h++)i=p(a,b[h]),j=q(e,o(i,b[h]).match(d)),j<g&&(g=j,c=i);return c}function s(a){this._d=a}function t(a,b,d){var e=c.relativeTime[a];return typeof e=="function"?e(b||1,!!d,a):e.replace(/%d/i,b||1)}function u(a,b){var c=d(Math.abs(a)/1e3),e=d(c/60),f=d(e/60),g=d(f/24),h=d(g/365),i=c<45&&["s",c]||e===1&&["m"]||e<45&&["mm",e]||f===1&&["h"]||f<22&&["hh",f]||g===1&&["d"]||g<=25&&["dd",g]||g<=45&&["M"]||g<345&&["MM",d(g/30)]||h===1&&["y"]||["yy",h];return i[2]=b,t.apply({},i)}function v(a,b){c.fn[a]=function(a){return a!=null?(this._d["set"+b](a),this):this._d["get"+b]()}}var c,d=Math.round,e={},f=typeof module!="undefined",g="months|monthsShort|weekdays|weekdaysShort|longDateFormat|relativeTime|ordinal|meridiem".split("|"),h,i="1.1.2",j="Month|Date|Hours|Minutes|Seconds".split("|");c=function(c,d){if(c===null)return null;var e;return c&&c._d instanceof a?e=new a(+c._d):d?m(d)?e=r(c,d):e=p(c,d):e=c===b?new a:c instanceof a?c:m(c)?n(c):new a(c),new s(e)},c.version=i,c.lang=function(a,b){var d,h,i;b&&(e[a]=b);if(e[a])for(d=0;d<g.length;d++)h=g[d],c[h]=e[a][h]||c[h];else f&&(i=require("./lang/"+a),c.lang(a,i))},c.lang("en",{months:"January_February_March_April_May_June_July_August_September_October_November_December".split("_"),monthsShort:"Jan_Feb_Mar_Apr_May_Jun_Jul_Aug_Sep_Oct_Nov_Dec".split("_"),weekdays:"Sunday_Monday_Tuesday_Wednesday_Thursday_Friday_Saturday".split("_"),weekdaysShort:"Sun_Mon_Tue_Wed_Thu_Fri_Sat".split("_"),longDateFormat:{L:"MM/DD/YYYY",LL:"MMMM D YYYY",LLL:"MMMM D YYYY h:mm A",LLLL:"dddd, MMMM D YYYY h:mm A"},meridiem:{AM:"AM",am:"am",PM:"PM",pm:"pm"},relativeTime:{future:"in %s",past:"%s ago",s:"a few seconds",m:"a minute",mm:"%d minutes",h:"an hour",hh:"%d hours",d:"a day",dd:"%d days",M:"a month",MM:"%d months",y:"a year",yy:"%d years"},ordinal:function(a){var b=a%10;return~~(a%100/10)===1?"th":b===1?"st":b===2?"nd":b===3?"rd":"th"}}),c.fn=s.prototype={clone:function(){return c(this)},valueOf:function(){return+this._d},"native":function(){return this._d},format:function(a){return o(this._d,a)},add:function(a,b){return this._d=l(this._d,a,1,b),this},subtract:function(a,b){return this._d=l(this._d,a,-1,b),this},diff:function(a,b,e){var f=c(a),g=this._d-f._d,h=this.year()-f.year(),i=this.month()-f.month(),j=this.day()-f.day(),k;return b==="months"?k=h*12+i+j/30:b==="years"?k=h+i/12:k=b==="seconds"?g/1e3:b==="minutes"?g/6e4:b==="hours"?g/36e5:b==="days"?g/864e5:b==="weeks"?g/6048e5:b==="days"?g/3600:g,e?k:d(k)},from:function(a,b){var d=this.diff(a),e=c.relativeTime,f=u(d,b);return b?f:(d<=0?e.past:e.future).replace(/%s/i,f)},fromNow:function(a){return this.from(c(),a)},isLeapYear:function(){var a=this._d.getFullYear();return a%4===0&&a%100!==0||a%400===0}};for(h=0;h<j.length;h++)v(j[h].toLowerCase(),j[h]);v("year","FullYear"),c.fn.day=function(){return this._d.getDay()},f&&(module.exports=c),typeof window!="undefined"&&(window.moment=c)})(Date);


// friend finder thingy z.a.
(function($) {
    $.fn.friendFinder = function(friends) {
        var $this = $(this),
            input = $this.find(".friend-finder-input"),
            hidden = $this.find("input[name='friends']"),
            data = {},
            dropdown = $this.find("#friend-finder-dropdown"),
            list = $("<div />").attr("id", "friend-finder-suggestions").appendTo(dropdown),
            selected = {},
            selectCount = 0,
            selectedItems = $this.find("#friend-finder-selected"),
            birthdays,
            cssHovered = 'friend-finder-hovered',
            limit = 20,
            once = false,
            mouseoverdropdown = false,
            prettyBirthdays = function(friends) {
                var d = new Date(), s, i, m = d.getMonth(), y = d.getFullYear();
                for(i in friends) {
                    if(friends[i].birthday) {
                        s = friends[i].birthday.split('/');
                        if((parseFloat(s[0]) == (m + 1)) && parseFloat(s[1]) == d.getDate()) {
                            friends[i].meta = 'Birthday today!';
                        } else if((parseFloat(s[0]) == (m + 1)) && parseFloat(s[1]) < d.getDate()) {
                            friends[i].meta = 'Birthday was ' + moment(s[0] + '/' + s[1] + '/' + y).fromNow();
                        } else if(parseFloat(s[0]) < (m + 1)) {
                            friends[i].meta = 'Birthday is ' + moment(s[0] + '/' + s[1] + '/' + (y + 1)).fromNow();
                        } else {
                            friends[i].meta = 'Birthday is ' + moment(s[0] + '/' + s[1] + '/' + y).fromNow();
                        }
                    }
                }
            },
            selectFriend = function() {
                if(selectCount >= limit) { dropdown.hide(); return; }
                var id = $(this).data('id');
                if(data[id] && !selected[id]) {
                    selected[id] = true;
                    $("<div class='friend-finder-selected-item'><div class='friend-finder-selected-text'>" + data[id].name + "</div><div class='friend-finder-selected-x'></div></div>").appendTo(selectedItems).data(data[id]);
                    selectCount++;
                    selectedItems.show();
                }
                input.val('').focus();
                $(this).remove();
                dropdown.hide();
                updateHidden();
            },
            updateHidden = function() {
                var ids = [];
                for(var i in selected) {
                    ids.push(i);
                }
                hidden.val(ids.join(','));
            },
            remove = function() {
                var id = $(this).closest('.friend-finder-selected-item').data('id');
                $(this).closest('.friend-finder-selected-item').remove();
                if(selected[id]) {
                    delete selected[id];
                    selectCount--;
                    if(!selectCount) {
                        selectedItems.hide();
                    }
                }
                updateHidden();
            },
            show = function(people) {
                if(!people || !'length' in people || !people.length) { return; }
                dropdown.show();
                people = people.slice(0,6);
                list.empty();
                var html = [], meta = '', i;
                for(i in people) {
                    /* Build a meta data string */
                    meta = '';
                    if(people[i].meta) {
                        meta = people[i].meta;
                    }
                    
                    /* Person list item */
                    html.push('<li class="friend-finder-person" data-id="' + people[i].id + '">');
                    
                    /* If there is a picture set, use it, otherwise use the graph pic */
                    if(people[i].picture) {
                        html.push("<img src='" + people[i].picture + "' />");
                    } else {
                        html.push("<img src='https://graph.facebook.com/" + people[i].id + "/picture' />");
                    }
                    
                    /* Add the name & birthday */
                    html.push("<div class='friend-finder-info'>");
                    html.push("<b>" + people[i].name + "</b>");
                    html.push(meta);
                    html.push("</div>");
                    
                    /* Close the item */
                    html.push('</li>');
                }
                list[0].innerHTML = html.join('');
                if(!list[0].innerHTML.length) { dropdown.hide(); }
                list.find('.friend-finder-person').first().addClass(cssHovered);
                list.css('top', $this.height());
            },
            keydown = function(e) {
                switch(e.which) {
                    case 27: /* escape */
                        mouseoverdropdown = false;
                        input.blur();
                        e.preventDefault();
                    break;
                    case 13: /* enter */
                        if(dropdown.is(':visible')) {
                            list.find('.friend-finder-hovered').click();
                        }
                        e.preventDefault();
                    break;
                    case 38: /* up arrow */
                        if(!list.find('.friend-finder-hovered').removeClass(cssHovered).prev().addClass(cssHovered).length) {
                            list.find('.friend-finder-person').last().addClass(cssHovered);
                        }
                        e.preventDefault();
                    break;
                    case 40: /* down arrow */
                        if(!list.find('.friend-finder-hovered').removeClass(cssHovered).next().addClass(cssHovered).length) {
                            list.find('.friend-finder-person').first().addClass(cssHovered);
                        }
                        e.preventDefault();
                    break;
                }
            },
            focus = function() {
                if(input.val().length) {
                    input.keyup();
                } else {
                    if(data && !once) {
                        once = true;
                        show(data);
                    }
                }
            },
            blur = function() {
                if(!mouseoverdropdown) {
                    dropdown.hide();
                    list.find('.friend-finder-person').remove();
                }
            },
            keyup = function(e) {
                if(e.which == 13 || e.which == 38 || e.which == 40) { return; }
                var val = input.val(), matches = [];
                if(!val.length) { return; }
                for(var i in data) {
                    if(!selected[data[i].id]) {
                        if(data[i].name.toLowerCase().indexOf(val.toLowerCase()) > -1) {
                            matches.push(data[i]);
                            continue;
                        } else if(data[i].username && data[i].username.replace('.', ' ').toLowerCase().indexOf(val.toLowerCase()) > -1) {
                            matches.push(data[i]);
                        }
                    }
                }
                if(matches.length) {
                    show(matches);
                } else {
                    dropdown.hide();
                }
            },
            addHighlight = function() {
                $(this).addClass(cssHovered).siblings().removeClass(cssHovered);
            },
            parseFriends = function(friends) {
                if(friends) {
                    for(var i = 0; i<friends.length; i++) {
                        data[friends[i].id] = friends[i];
                    }
                    prettyBirthdays(data);
                }
            },
            reset = function() {
                selected = {};
                selectedCount = 0;
                selectedItems.empty().hide();
                updateHidden();
            },
            selectId = function(id) {
                if(data[id]) {
                    input.val(data[id].name).keyup();
                    setTimeout(function() {
                        list.find('.friend-finder-person').each(function() {
                            var i = $(this);
                            if(i.data('id') == id) {
                                i.click();
                            }
                        });
                    }, 0);
                }
            }
        
        /* Make some methods available publicly */
        $this.data({'reset': reset, 'selectId': selectId});
        
        /* Parse the list of friends */
        parseFriends(friends.data);
        
        /* Add event listeners */
        input.keydown(keydown);
        input.keyup(keyup);
        input.blur(blur);
        input.focus(focus);
        list.delegate('.friend-finder-person', 'click', selectFriend);
        list.delegate('.friend-finder-person', 'mouseover', addHighlight);
        selectedItems.delegate('.friend-finder-selected-x', 'click', remove);
        dropdown.hover(function() { mouseoverdropdown = true; }, function() { mouseoverdropdown = false; });
    }
})(jQuery);