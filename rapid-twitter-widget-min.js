if(typeof(RapidTwitter)=="undefined"){RapidTwitter={}}RapidTwitter.script=function(c,e,h){var d=c.apis,b;function k(r,s){if(typeof s.error!="undefined"){return}var q=r.widgets,p=q.length,t="";t=g(r.screen_name,s);for(var o=0;o<p;o++){var n=q[o],m=h.createElement("ul");n=h.getElementById(n).parentNode;m.className="tweets";m.innerHTML=t;n.appendChild(m);j(n,"widget_twitter--hidden")}}c.callback=k;function g(x,s){var v="";if(typeof c.generate_html=="function"){return c.generate_html(x,s)}for(var q=0,o=s.length;q<o;q++){var r=s[q],t="",n=["tweet"];if(typeof r.user.screen_name=="undefined"){r.user.screen_name=x}if(typeof r.retweeted_status!="undefined"){r=r.retweeted_status;n.push("tweet--retweet");if(typeof r.user.screen_name=="undefined"){var m=s[q].entities.user_mentions,w=m.length,u=256;for(var p=0;p<w;p++){if(m[p].indices[0]<u){u=m[p].indices[0];r.user.screen_name=m[p].screen_name}}}t+="RT ";t+='<a href="';t+="https://twitter.com/";t+=r.user.screen_name;t+='" class="tweet__mention tweet__mention--retweet">';t+="<span>@</span>";t+=r.user.screen_name;t+="</a>";t+=": "}if(r.in_reply_to_screen_name!=null){n.push("tweet--reply")}v+='<li class="';v+=n.join(" ");v+='">';v+=t;v+=a(r);v+=" ";v+='<a class="tweet__datestamp timesince" href="';v+="https://twitter.com/";v+=r.user.screen_name;v+="/status/";v+=r.id_str;v+='">';v+=f(r.created_at);v+="</a>";v+="</li>"}return v}function f(n){var o=n.split(" "),p=new Date(o[1]+" "+o[2]+", "+o[5]+" "+o[3]+" UTC"),m=new Date(),q=(m.getTime()-p.getTime())/1000,i=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];if(q<60){return"less than a minute ago"}else{if(q<120){return"about a minute ago"}else{if(q<(45*60)){return(parseInt(q/60)).toString()+" minutes ago"}else{if(q<(90*60)){return"about an hour ago"}else{if(q<(24*60*60)){return"about "+(parseInt(q/3600)).toString()+" hours ago"}else{if(q<(48*60*60)){return"1 day ago"}else{return p.getDate()+" "+i[p.getMonth()]}}}}}}}c.relative_time=f;function a(s){var n=[],r=[],t=0,p,o,m,q;for(p in s.entities){for(o=0,m=s.entities[p].length;o<m;o++){q=s.entities[p][o];r[q.indices[0]]={end:q.indices[1],text:function(){switch(p){case"media":return'<a href="'+q.url+'" class="tweet__media" title="'+q.expanded_url+'">'+q.display_url+"</a>";break;case"urls":return(q.display_url)?'<a href="'+q.url+'" class="tweet__link" title="'+q.expanded_url+'">'+q.display_url+"</a>":q.url;break;case"user_mentions":var i=(q.indices[0]==0)?" tweet__mention--reply":"";return'<a href="https://twitter.com/'+q.screen_name+'" class="tweet__mention'+i+'"><span>@</span>'+q.screen_name+"</a>";break;case"hashtags":return'<a href="https://twitter.com/search?q=%23'+q.text+'" class="tweet__hashtag"><span>#</span>'+q.text+"</a>";break;default:return""}}()}}}for(o=0,m=r.length;o<m;o++){if(r[o]){q=r[o];n.push(s.text.substring(t,o));n.push(q.text);t=q.end;o=q.end-1}}n.push(s.text.substring(t));return n.join("")}c.process_entities=a;function j(i,n){var m=new RegExp("(\\s|^)"+n+"(\\s|$)");i.className=i.className.replace(m," ")}for(var l in d){(function(){var m=l,o=d[m],i=h.createElement("script"),n,p;p=("https:"==h.location.protocol?"https:":"http:");p+="//api.twitter.com/1/statuses/user_timeline.json?";p+="count=";p+=o.count;p+="&";p+="screen_name=";p+=o.screen_name;p+="&";p+="exclude_replies=";p+=o.exclude_replies;p+="&";p+="include_rts=";p+=o.include_rts;p+="&";p+="include_entities=";p+="t";p+="&";p+="trim_user=";p+="t";p+="&";p+="suppress_response_codes=";p+="t";p+="&";p+="callback=RapidTwitter.callback."+m+"";c.callback[m]=function(q){k(o,q)};i.type="text/javascript";i.async=true;i.src=p;n=h.getElementsByTagName("script")[0];n.parentNode.insertBefore(i,n)})()}}(RapidTwitter,window,document);