<div class="modal fade" tabindex="-1" role="dialog" id="sharing-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div style="padding: 35px 15px 25px 15px;" class="modal-body">
                <p style="font-size: 24px;" class="text-center">{{ $message }}</p>
                <div class="sharing-container">
                    <<div class="addthis_sharing_toolbox"></div>
                </div>
                <a href="#" id="skip-sharing" class="pull-right" data-dismiss="modal">Close</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@push('scripts')
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-579c836873a36a19"></script>
@endpush