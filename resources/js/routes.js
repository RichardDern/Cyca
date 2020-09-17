    var Ziggy = {
        namedRoutes: {"horizon.stats.index":{"uri":"horizon\/api\/stats","methods":["GET","HEAD"],"domain":null},"horizon.workload.index":{"uri":"horizon\/api\/workload","methods":["GET","HEAD"],"domain":null},"horizon.masters.index":{"uri":"horizon\/api\/masters","methods":["GET","HEAD"],"domain":null},"horizon.monitoring.index":{"uri":"horizon\/api\/monitoring","methods":["GET","HEAD"],"domain":null},"horizon.monitoring.store":{"uri":"horizon\/api\/monitoring","methods":["POST"],"domain":null},"horizon.monitoring-tag.paginate":{"uri":"horizon\/api\/monitoring\/{tag}","methods":["GET","HEAD"],"domain":null},"horizon.monitoring-tag.destroy":{"uri":"horizon\/api\/monitoring\/{tag}","methods":["DELETE"],"domain":null},"horizon.jobs-metrics.index":{"uri":"horizon\/api\/metrics\/jobs","methods":["GET","HEAD"],"domain":null},"horizon.jobs-metrics.show":{"uri":"horizon\/api\/metrics\/jobs\/{id}","methods":["GET","HEAD"],"domain":null},"horizon.queues-metrics.index":{"uri":"horizon\/api\/metrics\/queues","methods":["GET","HEAD"],"domain":null},"horizon.queues-metrics.show":{"uri":"horizon\/api\/metrics\/queues\/{id}","methods":["GET","HEAD"],"domain":null},"horizon.pending-jobs.index":{"uri":"horizon\/api\/jobs\/pending","methods":["GET","HEAD"],"domain":null},"horizon.completed-jobs.index":{"uri":"horizon\/api\/jobs\/completed","methods":["GET","HEAD"],"domain":null},"horizon.failed-jobs.index":{"uri":"horizon\/api\/jobs\/failed","methods":["GET","HEAD"],"domain":null},"horizon.failed-jobs.show":{"uri":"horizon\/api\/jobs\/failed\/{id}","methods":["GET","HEAD"],"domain":null},"horizon.retry-jobs.show":{"uri":"horizon\/api\/jobs\/retry\/{id}","methods":["POST"],"domain":null},"horizon.jobs.show":{"uri":"horizon\/api\/jobs\/{id}","methods":["GET","HEAD"],"domain":null},"horizon.index":{"uri":"horizon\/{view?}","methods":["GET","HEAD"],"domain":null},"login":{"uri":"login","methods":["GET","HEAD"],"domain":null},"logout":{"uri":"logout","methods":["POST"],"domain":null},"register":{"uri":"register","methods":["GET","HEAD"],"domain":null},"password.request":{"uri":"password\/reset","methods":["GET","HEAD"],"domain":null},"password.email":{"uri":"password\/email","methods":["POST"],"domain":null},"password.reset":{"uri":"password\/reset\/{token}","methods":["GET","HEAD"],"domain":null},"password.update":{"uri":"password\/reset","methods":["POST"],"domain":null},"password.confirm":{"uri":"password\/confirm","methods":["GET","HEAD"],"domain":null},"home":{"uri":"\/","methods":["GET","HEAD"],"domain":null},"document.move":{"uri":"document\/move\/{sourceFolder}\/{targetFolder}","methods":["POST"],"domain":null},"document.destroy_bookmarks":{"uri":"document\/delete_bookmarks\/{folder}","methods":["POST"],"domain":null},"document.visit":{"uri":"document\/{document}\/visit\/{folder}","methods":["POST"],"domain":null},"feed_item.mark_as_read":{"uri":"feed_item\/mark_as_read","methods":["POST"],"domain":null},"feed.ignore":{"uri":"feed\/{feed}\/ignore","methods":["POST"],"domain":null},"feed.follow":{"uri":"feed\/{feed}\/follow","methods":["POST"],"domain":null},"folder.index":{"uri":"folder","methods":["GET","HEAD"],"domain":null},"folder.create":{"uri":"folder\/create","methods":["GET","HEAD"],"domain":null},"folder.store":{"uri":"folder","methods":["POST"],"domain":null},"folder.show":{"uri":"folder\/{folder}","methods":["GET","HEAD"],"domain":null},"folder.edit":{"uri":"folder\/{folder}\/edit","methods":["GET","HEAD"],"domain":null},"folder.update":{"uri":"folder\/{folder}","methods":["PUT","PATCH"],"domain":null},"folder.destroy":{"uri":"folder\/{folder}","methods":["DELETE"],"domain":null},"document.index":{"uri":"document","methods":["GET","HEAD"],"domain":null},"document.create":{"uri":"document\/create","methods":["GET","HEAD"],"domain":null},"document.store":{"uri":"document","methods":["POST"],"domain":null},"document.show":{"uri":"document\/{document}","methods":["GET","HEAD"],"domain":null},"document.edit":{"uri":"document\/{document}\/edit","methods":["GET","HEAD"],"domain":null},"document.update":{"uri":"document\/{document}","methods":["PUT","PATCH"],"domain":null},"document.destroy":{"uri":"document\/{document}","methods":["DELETE"],"domain":null},"feed_item.index":{"uri":"feed_item","methods":["GET","HEAD"],"domain":null},"feed_item.create":{"uri":"feed_item\/create","methods":["GET","HEAD"],"domain":null},"feed_item.store":{"uri":"feed_item","methods":["POST"],"domain":null},"feed_item.show":{"uri":"feed_item\/{feed_item}","methods":["GET","HEAD"],"domain":null},"feed_item.edit":{"uri":"feed_item\/{feed_item}\/edit","methods":["GET","HEAD"],"domain":null},"feed_item.update":{"uri":"feed_item\/{feed_item}","methods":["PUT","PATCH"],"domain":null},"feed_item.destroy":{"uri":"feed_item\/{feed_item}","methods":["DELETE"],"domain":null}},
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
