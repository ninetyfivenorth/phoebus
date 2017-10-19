<img src="{$PAGE_DATA.metadata.icon}" style="height: 48px; width: 48px; margin-top: 22px;" class="alignright">
<h1>
    {$PAGE_DATA.metadata.name}
</h1>

<p style="margin-top: -18px">
    By: {$PAGE_DATA.metadata.author}
</p>

<h3>
    About this {$PAGE_DATA.addon.type}
</h3>

<p>
    {$PAGE_DATA.metadata.longDescription}
</p>

{if $PAGE_DATA.metadata.license == 'copyright'}
<h3>
    Copyright Notice
</h3>
<p>
{if $PAGE_DATA.metadata.licenseDefault == true}
    The developer of this {$PAGE_DATA.addon.type} has not indicated that it is under any kind of licensing. So, unless otherwise indicated this {$PAGE_DATA.addon.type} is:<br /><br />
{/if}
    <a href="/?component=license&id={$PAGE_DATA.addon.id}" target="_blank">{$PAGE_DATA.metadata.licenseName}</a>
</p>
{/if}

{if $PAGE_DATA.metadata.hasPreview == true}
    <img src="{$PAGE_DATA.metadata.preview}" class="aligncenter" style="max-width: 750px"/>
{/if}

<p style="text-align: center; padding: 10px;">
    <a class="dllink_green" href="/?component=download&id={$PAGE_DATA.addon.id}&version={$PAGE_DATA['xpinstall'][$PAGE_DATA['addon']['release']]['version']}&hash={$PAGE_DATA['xpinstall'][$PAGE_DATA['addon']['release']]['hash']}">
        <img border="0" src="{$BASE_PATH}download.png" alt="" style="width: 24px; height: 24px; position: relative; top: 7px; right: 4px;" />
        Install {$PAGE_DATA.metadata.name}
    </a>
</p>

</div> <!-- END DIV ID PM-Content-Body -->
<div id="PM-Content-Sidebar"> <!-- START PM-Content-Sidebar -->
    <div style="margin-top: 22px;">
        <h3>Release Information</h3>
        Version {$PAGE_DATA['xpinstall'][$PAGE_DATA['addon']['release']]['version']}<br />
        Updated on {$PAGE_DATA['xpinstall'][$PAGE_DATA['addon']['release']]['prettyDate']}
        <p>
            
        </p>
        
        <h3>
            Compatibility
        </h3>

        <p>
            Pale Moon {$PAGE_DATA['xpinstall'][$PAGE_DATA['addon']['release']]['minAppVersion']} to 
{if $PAGE_DATA['xpinstall'][$PAGE_DATA['addon']['release']]['maxAppVersion'] == '*'}
            Unknown
{else}
            {$PAGE_DATA['xpinstall'][$PAGE_DATA['addon']['release']]['maxAppVersion']}
{/if}
        </p>

        <h3>
            
        </h3>
        <p>
            
        </p>

{if $PAGE_DATA.metadata.license != null && $PAGE_DATA.metadata.license != 'copyright'}
        <h3>
            License
        </h3>
        <p>
            <a href="/?component=license&id={$PAGE_DATA.addon.id}" target="_blank">{$PAGE_DATA.metadata.licenseName}</a>
        </p>
{/if}

{if $PAGE_DATA.metadata.homepageURL != null || $PAGE_DATA.metadata.supportURL != null || $PAGE_DATA.metadata.supportEmail != null || $PAGE_DATA.metadata.repository != null}
        <h3>
            Resources
        </h3>
        <p>
{if $PAGE_DATA.metadata.homepageURL != null}
            <a href="{$PAGE_DATA.metadata.homepageURL}" target="_blank">Add-on Homepage</a><br />
{/if}
{if $PAGE_DATA.metadata.supportURL != null}
            <a href="{$PAGE_DATA.metadata.supportURL}" target="_blank">Support Site</a><br />
{/if}
{if $PAGE_DATA.metadata.supportEmail != null}
            <a href="mailto:{$PAGE_DATA.metadata.supportEmail}">Support E-mail</a><br />
{/if}
{if $PAGE_DATA.metadata.repository != null}
            <a href="{$PAGE_DATA.metadata.repository}" target="_blank">Source Repository</a><br />
{/if}
        </p>
{/if}

{if $PAGE_DATA.xpinstall|@count > 1}
        <h3>
            Previous Releases
        </h3>
        <div id="addonOldVersions" style="overflow-y: hidden; height: 140px; padding: 0px">
{foreach $PAGE_DATA.xpinstall as $key}
{if $key != $PAGE_DATA['xpinstall'][$PAGE_DATA['addon']['release']]}
            <a href="/?component=download&id={$PAGE_DATA.addon.id}&version={$key.version}&hash={$key.hash}">Version {$key.version}</a> <small>[{$key.date}]</small><br />
            <small>
                Works with Pale Moon {$key.minAppVersion} to
{if $key.maxAppVersion == '*'}
                Unknown
{else}
                {$key.maxAppVersion}
{/if}
            </small><br /><br />
{/if}
{/foreach}
        </div>
{if $PAGE_DATA.xpinstall|@count > 4}
        <br /><small><a id="addonShowMore" href="#" onclick="document.getElementById('addonOldVersions').style.height = null; document.getElementById('addonShowMore').style.display = 'none'">Show all&hellip;</a></small>
{/if}
{/if}
    </div>
    <div class="clearfix"></div>
{$key = null}