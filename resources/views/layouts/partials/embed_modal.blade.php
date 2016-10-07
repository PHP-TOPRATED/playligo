<div class="modal fade" tabindex="-1" role="dialog" id="embed_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">@if(isset($title)){{$title}}@endif</h4>
            </div>
            <div style="padding: 35px 15px 25px 15px;" class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#share_tab" aria-controls="share_tab" role="tab"
                                                              data-toggle="tab">Share</a></li>
                    <li role="presentation"><a href="#embed_tab" aria-controls="embed_tab" role="tab" data-toggle="tab">Embed</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content share_embed_content">
                    <div role="tabpanel" class="tab-pane active" id="share_tab">
                        <div class="sharing-container">
                            <div class="addthis_sharing_toolbox"></div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="embed_tab">
                        <input class="form-control" id="embed_input" value="{{ $embed_code }}">
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@push('scripts')
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-579c836873a36a19"></script>
<script>
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('#embed_input').focus();
        $('#embed_input').select();
    })
</script>
@endpush