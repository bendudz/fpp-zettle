<h1 id="announce-zettle-">Announce Zettle!</h1>
<p><a href="http://makeapullrequest.com"><img src="https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat" alt="Pull Requests Welcome"></a>
    <img src="https://shields.io/badge/fpp-AnnounceZettle-brightgreen" alt="FPP Zettle Logo"></p>
<p>Get yourself an iZettle device, take a donation &amp; trigger an effect on your light show.</p>
<h2 id="getting-started">Getting Started</h2>
<p>You&#39;ll need an iZettle device to use this plugin.</p>
<p>Either <a href="https://register.zettle.com/gb">register</a> or <a href="https://login.zettle.com/">login</a> with Zettle.</p>
<ul>
    <li>Click &#39;Integrations&#39; (Bottom Left)</li>
    <li>Click &#39;API Keys&#39;
        <img style='height: 100%; width: 100%; object-fit: contain' src="/plugin.php?plugin=fpp-zettle&page=img/zettle-api-key.png&nopage=1" alt="Zettle API Integrations"></li>
    <li>Click &#39;Create API Key&#39;</li>
    <li>Type a name for your API Key.</li>
    <li>Select &#39;READ:USERINFO and READ:PURCHASE&#39;
        <img src="/plugin.php?plugin=fpp-zettle&page=img/zettle-apikeys.png&nopage=1" alt="1"></li>
    <li>Click &#39;Create Key&#39;</li>
</ul>
<p>You will now be presented with 2 attributes you need to copy &amp; keep safe. They won&#39;t be retrievable again so make sure you capture them:</p>
<ul>
    <li>client_id</li>
    <li>API Key
        <img style='height: 100%; width: 100%; object-fit: contain' src="/plugin.php?plugin=fpp-zettle&page=img/zettle-apikeys-created.png&nopage=1" alt="1"></li>
</ul>
<h2 id="dataplicity-setup">Dataplicity Setup</h2>
<p>This plugin relies on a secure https endpoint so Zettle can send events to you. The easiest way to set up a https endpoint is to use <a href="https://www.dataplicity.com">Dataplicity</a></p>
<p>Greg Macaree has produced an excellent <a href="https://youtu.be/7LeD3dz-uXU">getting started video</a> for Dataplicity. You need to enable the wormhole setting and save the address, you&#39;ll need this later. Watch Greg&#39;s tutorial here:</p>
<iframe width="560" height="315" src="https://www.youtube.com/embed/7LeD3dz-uXU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
<h2 id="fpp">FPP</h2>
<p>Navigate to your FPP instance. </p>
<p>Click <code>&#39;Content Setup&#39; &gt; &#39;Plugin Manager&#39;</code></p>
<p>Install the <code>Announce Zettle</code> plugin.</p>
<p>Once installed, navigate to <code>&#39;Content Setup&#39; &gt; &#39;Zettle - Setup&#39;</code>.</p>
<p><img style='height: 100%; width: 100%; object-fit: contain' src="/plugin.php?plugin=fpp-zettle&page=img/setup-init.png&nopage=1" alt="1"></p>
<p>Add your <code>Client ID</code> and <code>Secret</code> to the page &amp; click &#39;Save&#39;</p>
<p><img src="/plugin.php?plugin=fpp-zettle&page=img/create-sub.png&nopage=1" alt=""></p>
<p>This will now unlock the ability to add a <code>Subscription</code> that will listen for &#39;purchases&#39; aka donations from our Zettle device.</p>
<p>Enter your Dataplicity wormhole address followed by this plugin&#39;s API event path. ie</p>
<p><code>https://{wormhole address}/api/plugin/fpp-zettle/event</code></p>
<p><code>https://wandering-sheep-0157.dataplicity.io/api/plugin/fpp-zettle/event</code></p>
<p>Add your email address too. This is the address that is notified of any errors sending (or in Zettle&#39;s terminology, &#39;pushing&#39;) a transaction to your Pi.</p>
<p>Save the subscription.</p>
<p><img style='height: 100%; width: 100%; object-fit: contain' src="/plugin.php?plugin=fpp-zettle&page=img/save-sub.png&nopage=1" alt="1"></p>
<p>The first time you create a subscription you will receive a test notification sent to your Raspberry Pi. This is just the Zettle API notifying you that a subscription has been set up.</p>
<p>Once the subscription has been created successfully, you can then add an effect to be triggered. </p>
<p>Navigate back to the set-up page where you can select the effect to trigger once a transaction is received.</p>
<p><img style='height: 100%; width: 100%; object-fit: contain' src="/plugin.php?plugin=fpp-zettle&page=img/add-effect-trigger.png&nopage=1" alt="1"></p>
<p>At the moment this plugin can only trigger an effect (ESEQ file), if you have a use case for triggering something else please raise an issue on GitHub, so we can add support.</p>
<p>When a real transaction is received the plugin will log it to a transaction file. You can view transactions in <code>Status / Control &gt; Zettle - Status</code>. This page will also allow you to clear any transactions should you wish. This is mearly for you to see what / who has used your Zettle device to donate at your show.</p>
<p><img style='height: 100%; width: 100%; object-fit: contain' src="/plugin.php?plugin=fpp-zettle&page=img/status-page.png&nopage=1" alt="1"></p>
<h2 id="privacy-policy">Privacy Policy</h2>
<h3 id="what-we-collect">What We Collect</h3>
<p>Absolutely nothing!</p>
<h3 id="what-we-don-t-collect">What We Don&#39;t Collect</h3>
<p>We do not collect or store any of your personal information. The information you submit via this plugin is transmitted between your Pi &amp; the Zettle API. Any transactions are kept on your device &amp; are retrievable from Zettle using your API Keys (client_id &amp; secret) should you clear them. </p>
