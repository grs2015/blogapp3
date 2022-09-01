const Ziggy = {"url":"http:\/\/localhost:8000","port":8000,"defaults":{},"routes":{"debugbar.openhandler":{"uri":"_debugbar\/open","methods":["GET","HEAD"]},"debugbar.clockwork":{"uri":"_debugbar\/clockwork\/{id}","methods":["GET","HEAD"]},"debugbar.telescope":{"uri":"_debugbar\/telescope\/{id}","methods":["GET","HEAD"]},"debugbar.assets.css":{"uri":"_debugbar\/assets\/stylesheets","methods":["GET","HEAD"]},"debugbar.assets.js":{"uri":"_debugbar\/assets\/javascript","methods":["GET","HEAD"]},"debugbar.cache.delete":{"uri":"_debugbar\/cache\/{key}\/{tags?}","methods":["DELETE"]},"login":{"uri":"login","methods":["GET","HEAD"]},"logout":{"uri":"logout","methods":["POST"]},"password.request":{"uri":"forgot-password","methods":["GET","HEAD"]},"password.reset":{"uri":"reset-password\/{token}","methods":["GET","HEAD"]},"password.email":{"uri":"forgot-password","methods":["POST"]},"password.update":{"uri":"reset-password","methods":["POST"]},"register":{"uri":"register","methods":["GET","HEAD"]},"user-profile-information.update":{"uri":"user\/profile-information","methods":["PUT"]},"user-password.update":{"uri":"user\/password","methods":["PUT"]},"password.confirmation":{"uri":"user\/confirmed-password-status","methods":["GET","HEAD"]},"password.confirm":{"uri":"user\/confirm-password","methods":["POST"]},"telescope":{"uri":"telescope\/{view?}","methods":["GET","HEAD"],"wheres":{"view":"(.*)"}},"ignition.healthCheck":{"uri":"_ignition\/health-check","methods":["GET","HEAD"]},"ignition.executeSolution":{"uri":"_ignition\/execute-solution","methods":["POST"]},"ignition.updateConfig":{"uri":"_ignition\/update-config","methods":["POST"]},"posts.index":{"uri":"posts","methods":["GET","HEAD"]},"posts.show":{"uri":"posts\/{post}","methods":["GET","HEAD"],"bindings":{"post":"slug"}},"public.baseinfo.index":{"uri":"baseinfo","methods":["GET","HEAD"]},"public.categories.index":{"uri":"categories","methods":["GET","HEAD"]},"public.categories.show":{"uri":"categories\/{category}","methods":["GET","HEAD"],"bindings":{"category":"slug"}},"public.tags.index":{"uri":"tags","methods":["GET","HEAD"]},"public.tags.show":{"uri":"tags\/{tag}","methods":["GET","HEAD"],"bindings":{"tag":"slug"}},"admin.index":{"uri":"admin","methods":["GET","HEAD"]},"admin.avatar.update":{"uri":"admin\/avatar","methods":["PUT"]},"admin.avatar.delete":{"uri":"admin\/avatar","methods":["POST"]},"admin.tags.index":{"uri":"admin\/tags","methods":["GET","HEAD"]},"admin.tags.create":{"uri":"admin\/tags\/create","methods":["GET","HEAD"]},"admin.tags.store":{"uri":"admin\/tags","methods":["POST"]},"admin.tags.show":{"uri":"admin\/tags\/{tag}","methods":["GET","HEAD"],"bindings":{"tag":"slug"}},"admin.tags.edit":{"uri":"admin\/tags\/{tag}\/edit","methods":["GET","HEAD"],"bindings":{"tag":"slug"}},"admin.tags.update":{"uri":"admin\/tags\/{tag}","methods":["PUT","PATCH"]},"admin.tags.destroy":{"uri":"admin\/tags\/{tag}","methods":["DELETE"],"bindings":{"tag":"slug"}},"admin.tagdelete":{"uri":"admin\/tagmassdelete","methods":["POST"]},"admin.categories.index":{"uri":"admin\/categories","methods":["GET","HEAD"]},"admin.categories.create":{"uri":"admin\/categories\/create","methods":["GET","HEAD"]},"admin.categories.store":{"uri":"admin\/categories","methods":["POST"]},"admin.categories.show":{"uri":"admin\/categories\/{category}","methods":["GET","HEAD"],"bindings":{"category":"slug"}},"admin.categories.edit":{"uri":"admin\/categories\/{category}\/edit","methods":["GET","HEAD"],"bindings":{"category":"slug"}},"admin.categories.update":{"uri":"admin\/categories\/{category}","methods":["PUT","PATCH"]},"admin.categories.destroy":{"uri":"admin\/categories\/{category}","methods":["DELETE"],"bindings":{"category":"slug"}},"admin.catdelete":{"uri":"admin\/catmassdelete","methods":["POST"]},"admin.baseinfo.index":{"uri":"admin\/baseinfo","methods":["GET","HEAD"]},"admin.baseinfo.create":{"uri":"admin\/baseinfo\/create","methods":["GET","HEAD"]},"admin.baseinfo.store":{"uri":"admin\/baseinfo","methods":["POST"]},"admin.baseinfo.show":{"uri":"admin\/baseinfo\/{baseinfo}","methods":["GET","HEAD"],"bindings":{"baseinfo":"id"}},"admin.baseinfo.edit":{"uri":"admin\/baseinfo\/{baseinfo}\/edit","methods":["GET","HEAD"],"bindings":{"baseinfo":"id"}},"admin.baseinfo.update":{"uri":"admin\/baseinfo\/{baseinfo}","methods":["PUT","PATCH"]},"admin.baseinfo.destroy":{"uri":"admin\/baseinfo\/{baseinfo}","methods":["DELETE"],"bindings":{"baseinfo":"id"}},"admin.posts.postmetas.index":{"uri":"admin\/posts\/{post}\/postmetas","methods":["GET","HEAD"],"bindings":{"post":"slug"}},"admin.posts.postmetas.create":{"uri":"admin\/posts\/{post}\/postmetas\/create","methods":["GET","HEAD"]},"admin.posts.postmetas.store":{"uri":"admin\/posts\/{post}\/postmetas","methods":["POST"],"bindings":{"post":"slug"}},"admin.posts.postmetas.show":{"uri":"admin\/posts\/{post}\/postmetas\/{postmeta}","methods":["GET","HEAD"],"bindings":{"post":"slug","postmeta":"id"}},"admin.posts.postmetas.edit":{"uri":"admin\/posts\/{post}\/postmetas\/{postmeta}\/edit","methods":["GET","HEAD"],"bindings":{"post":"slug","postmeta":"id"}},"admin.posts.postmetas.update":{"uri":"admin\/posts\/{post}\/postmetas\/{postmeta}","methods":["PUT","PATCH"],"bindings":{"post":"slug"}},"admin.posts.postmetas.destroy":{"uri":"admin\/posts\/{post}\/postmetas\/{postmeta}","methods":["DELETE"],"bindings":{"post":"slug","postmeta":"id"}},"admin.posts.comments.index":{"uri":"admin\/posts\/{post}\/comments","methods":["GET","HEAD"],"bindings":{"post":"slug"}},"admin.posts.comments.create":{"uri":"admin\/posts\/{post}\/comments\/create","methods":["GET","HEAD"]},"admin.posts.comments.store":{"uri":"admin\/posts\/{post}\/comments","methods":["POST"],"bindings":{"post":"slug"}},"admin.posts.comments.show":{"uri":"admin\/posts\/{post}\/comments\/{comment}","methods":["GET","HEAD"],"bindings":{"post":"slug","comment":"id"}},"admin.posts.comments.edit":{"uri":"admin\/posts\/{post}\/comments\/{comment}\/edit","methods":["GET","HEAD"],"bindings":{"post":"slug","comment":"id"}},"admin.posts.comments.update":{"uri":"admin\/posts\/{post}\/comments\/{comment}","methods":["PUT","PATCH"],"bindings":{"post":"slug"}},"admin.posts.comments.destroy":{"uri":"admin\/posts\/{post}\/comments\/{comment}","methods":["DELETE"],"bindings":{"post":"slug","comment":"id"}},"admin.users.index":{"uri":"admin\/users","methods":["GET","HEAD"]},"admin.users.create":{"uri":"admin\/users\/create","methods":["GET","HEAD"]},"admin.users.store":{"uri":"admin\/users","methods":["POST"]},"admin.users.show":{"uri":"admin\/users\/{user}","methods":["GET","HEAD"],"bindings":{"user":"id"}},"admin.users.edit":{"uri":"admin\/users\/{user}\/edit","methods":["GET","HEAD"],"bindings":{"user":"id"}},"admin.users.update":{"uri":"admin\/users\/{user}","methods":["PUT","PATCH"]},"admin.users.destroy":{"uri":"admin\/users\/{user}","methods":["DELETE"],"bindings":{"user":"id"}},"admin.users.forcedelete":{"uri":"admin\/users\/delete","methods":["POST"]},"admin.users.restore":{"uri":"admin\/users\/restore","methods":["POST"]},"admin.users.status":{"uri":"admin\/users\/status","methods":["POST"]},"admin.posts.index":{"uri":"admin\/posts","methods":["GET","HEAD"]},"admin.posts.create":{"uri":"admin\/posts\/create","methods":["GET","HEAD"]},"admin.posts.store":{"uri":"admin\/posts","methods":["POST"]},"admin.posts.show":{"uri":"admin\/posts\/{post}","methods":["GET","HEAD"],"bindings":{"post":"slug"}},"admin.posts.edit":{"uri":"admin\/posts\/{post}\/edit","methods":["GET","HEAD"],"bindings":{"post":"slug"}},"admin.posts.update":{"uri":"admin\/posts\/{post}","methods":["PUT","PATCH"]},"admin.posts.destroy":{"uri":"admin\/posts\/{post}","methods":["DELETE"],"bindings":{"post":"slug"}},"admin.hero_image.delete":{"uri":"admin\/hero_image","methods":["POST"]},"admin.gallery_image.delete":{"uri":"admin\/gallery_image","methods":["POST"]},"admin.posts.forcedelete":{"uri":"admin\/posts\/delete","methods":["POST"]},"admin.posts.restore":{"uri":"admin\/posts\/restore","methods":["POST"]},"admin.posts.status":{"uri":"admin\/posts\/status","methods":["POST"]},"admin.posts.favorite":{"uri":"admin\/posts\/favorite","methods":["POST"]},"admin.baseinfos.edit":{"uri":"admin\/baseinfos\/{baseinfo}\/edit","methods":["GET","HEAD"],"bindings":{"baseinfo":"id"}},"admin.baseinfos.update":{"uri":"admin\/baseinfos\/{baseinfo}","methods":["PUT","PATCH"]},"author.index":{"uri":"author","methods":["GET","HEAD"]},"author.tags.index":{"uri":"author\/tags","methods":["GET","HEAD"]},"author.tags.show":{"uri":"author\/tags\/{tag}","methods":["GET","HEAD"],"bindings":{"tag":"slug"}},"author.categories.index":{"uri":"author\/categories","methods":["GET","HEAD"]},"author.categories.show":{"uri":"author\/categories\/{category}","methods":["GET","HEAD"],"bindings":{"category":"slug"}},"author.posts.postmetas.index":{"uri":"author\/posts\/{post}\/postmetas","methods":["GET","HEAD"],"bindings":{"post":"slug"}},"author.posts.postmetas.create":{"uri":"author\/posts\/{post}\/postmetas\/create","methods":["GET","HEAD"]},"author.posts.postmetas.store":{"uri":"author\/posts\/{post}\/postmetas","methods":["POST"],"bindings":{"post":"slug"}},"author.posts.postmetas.show":{"uri":"author\/posts\/{post}\/postmetas\/{postmeta}","methods":["GET","HEAD"],"bindings":{"post":"slug","postmeta":"id"}},"author.posts.postmetas.edit":{"uri":"author\/posts\/{post}\/postmetas\/{postmeta}\/edit","methods":["GET","HEAD"],"bindings":{"post":"slug","postmeta":"id"}},"author.posts.postmetas.update":{"uri":"author\/posts\/{post}\/postmetas\/{postmeta}","methods":["PUT","PATCH"],"bindings":{"post":"slug","postmeta":"id"}},"author.posts.postmetas.destroy":{"uri":"author\/posts\/{post}\/postmetas\/{postmeta}","methods":["DELETE"],"bindings":{"post":"slug","postmeta":"id"}},"author.posts.index":{"uri":"author\/posts","methods":["GET","HEAD"]},"author.posts.create":{"uri":"author\/posts\/create","methods":["GET","HEAD"]},"author.posts.store":{"uri":"author\/posts","methods":["POST"]},"author.posts.show":{"uri":"author\/posts\/{post}","methods":["GET","HEAD"],"bindings":{"post":"slug"}},"author.posts.edit":{"uri":"author\/posts\/{post}\/edit","methods":["GET","HEAD"],"bindings":{"post":"slug"}},"author.posts.update":{"uri":"author\/posts\/{post}","methods":["PUT","PATCH"]},"author.posts.destroy":{"uri":"author\/posts\/{post}","methods":["DELETE"],"bindings":{"post":"slug"}},"author.hero_image.delete":{"uri":"author\/hero_image","methods":["POST"]},"author.gallery_image.delete":{"uri":"author\/gallery_image","methods":["POST"]},"author.posts.status":{"uri":"author\/posts\/status","methods":["POST"]},"author.users.edit":{"uri":"author\/users\/{user}\/edit","methods":["GET","HEAD"],"bindings":{"user":"id"}},"author.users.update":{"uri":"author\/users\/{user}","methods":["PUT","PATCH"]},"author.avatar.update":{"uri":"author\/avatar","methods":["PUT"]},"author.avatar.delete":{"uri":"author\/avatar","methods":["POST"]},"memberposts.comments.create":{"uri":"member\/posts\/{post}\/comments\/create","methods":["GET","HEAD"]},"memberposts.comments.store":{"uri":"member\/posts\/{post}\/comments","methods":["POST"],"bindings":{"post":"slug"}},"memberrate":{"uri":"member\/posts\/{post}\/rate","methods":["POST"],"bindings":{"post":"slug"}}}};

if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
