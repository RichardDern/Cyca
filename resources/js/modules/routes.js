    var Ziggy = {
        namedRoutes: {"logout":{"uri":"logout","methods":["POST"],"domain":null},"account":{"uri":"account","methods":["GET","HEAD"],"domain":null},"home":{"uri":"\/","methods":["GET","HEAD"],"domain":null},"account.password":{"uri":"account\/password","methods":["GET","HEAD"],"domain":null},"account.theme":{"uri":"account\/theme","methods":["GET","HEAD"],"domain":null},"account.setTheme":{"uri":"account\/theme","methods":["POST"],"domain":null},"account.theme.details":{"uri":"account\/theme\/details\/{name}","methods":["GET","HEAD"],"domain":null},"account.getThemes":{"uri":"account\/theme\/themes","methods":["GET","HEAD"],"domain":null},"account.import.form":{"uri":"account\/import","methods":["GET","HEAD"],"domain":null},"account.import":{"uri":"account\/import","methods":["POST"],"domain":null},"account.export":{"uri":"account\/export","methods":["GET","HEAD"],"domain":null},"document.move":{"uri":"document\/move\/{sourceFolder}\/{targetFolder}","methods":["POST"],"domain":null},"document.destroy_bookmarks":{"uri":"document\/delete_bookmarks\/{folder}","methods":["POST"],"domain":null},"document.visit":{"uri":"document\/{document}\/visit\/{folder}","methods":["POST"],"domain":null},"feed_item.mark_as_read":{"uri":"feed_item\/mark_as_read","methods":["POST"],"domain":null},"feed.ignore":{"uri":"feed\/{feed}\/ignore","methods":["POST"],"domain":null},"feed.follow":{"uri":"feed\/{feed}\/follow","methods":["POST"],"domain":null},"folder.index":{"uri":"folder","methods":["GET","HEAD"],"domain":null},"folder.store":{"uri":"folder","methods":["POST"],"domain":null},"folder.show":{"uri":"folder\/{folder}","methods":["GET","HEAD"],"domain":null},"folder.update":{"uri":"folder\/{folder}","methods":["PUT","PATCH"],"domain":null},"folder.destroy":{"uri":"folder\/{folder}","methods":["DELETE"],"domain":null},"document.index":{"uri":"document","methods":["GET","HEAD"],"domain":null},"document.store":{"uri":"document","methods":["POST"],"domain":null},"document.show":{"uri":"document\/{document}","methods":["GET","HEAD"],"domain":null},"feed_item.index":{"uri":"feed_item","methods":["GET","HEAD"],"domain":null},"feed_item.show":{"uri":"feed_item\/{feed_item}","methods":["GET","HEAD"],"domain":null},"highlight.store":{"uri":"highlight","methods":["POST"],"domain":null},"highlight.update":{"uri":"highlight\/{highlight}","methods":["PUT","PATCH"],"domain":null},"highlight.destroy":{"uri":"highlight\/{highlight}","methods":["DELETE"],"domain":null}},
        baseUrl: 'https://cyca.athaliasoft.com/',
        baseProtocol: 'https',
        baseDomain: 'cyca.athaliasoft.com',
        basePort: false,
        defaultParameters: []
    };

    if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
        for (var name in window.Ziggy.namedRoutes) {
            Ziggy.namedRoutes[name] = window.Ziggy.namedRoutes[name];
        }
    }

    export {
        Ziggy
    }
