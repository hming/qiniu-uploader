@extends('layouts.app')
<style>
    .successMsg{
        display: none;
    }
</style>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">上传图片</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @auth
                        <form data-url="{{ route('upload') }}"  id="form">
                            <div>
                                <span>文件名：</span>
                                images/<input type="text" name="target" placeholder="文件名，例如资源图片为 assets/default.png" style="width:550px;"/>
                            </div>
                            <br/>
                            <div>
                                <span>文件：</span>
                                <input type="file" name="file"/>
                            </div>
                            <br/>
                            <button type="reset" style="width: 80px;margin-right: 20px;" id="restBtn">重置</button>
                            <button type="button"  id="uploaderBtn" style="width: 200px;">上传</button>
                        </form>
                        <br/>
                        <span class="successMsg" style="color: green;">上传成功</span>
                        <span class="successMsg">文件路径：<a href="" id="qiniuUrl"></a> </span>
                    @endauth
                </div>


            </div>
        </div>
    </div>
</div>
@endsection

<script src="/js/jquery.min.js"></script>
<script type="application/javascript">
    $(document).ready(function () {
        $('#uploaderBtn').click(function () {
            var form_data = new FormData($('#form')[0]);
            var url = $('#form').data('url');
            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: url,
                type: 'POST',
                processData: false,
                contentType: false,
                async: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': token,
                },
                data: form_data
            }).done(function (result) {
                $('.successMsg').show();
                $('#qiniuUrl').attr('href',result.url);
                $('#qiniuUrl').text(result.url);
            }).fail(function (result) {
                alert('上传失败');
            });
        });


        $('#restBtn').click(function () {
            $('.successMsg').hide();
        });

    });
</script>
