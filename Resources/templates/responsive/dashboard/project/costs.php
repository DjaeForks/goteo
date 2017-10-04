<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1><?= $this->text('costs-main-header') ?></h1>
    <p><?= $this->text('guide-project-costs') ?></p>

    <?= $this->supply('dashboard-content-form', function() {
        $form = $this->raw('form');
        echo $this->form_start($form);

        echo $this->form_row($form['one_round']);
        echo $this->form_row($form['title-costs']);

        $min = $opt = 0;
        echo '<div class="cost-list">';
        foreach($this->costs as $cost) {
            if($cost->required) $min += $cost->amount;
            else                $opt += $cost->amount;
            echo $this->insert('dashboard/project/partials/cost_item', ['cost' => $cost, 'form' => $form]);
        }
        echo '</div>';
        // echo '<div class="form-group"><button class="btn btn-default btn-lg add-cost"><i class="fa fa-plus"></i> ' . $this->text('project-add-cost') . '</button></div>';
        echo '<div class="form-group">'.$this->form_row($form['add-cost']).'</div>';

        echo $this->insert('dashboard/project/partials/costs_bar', ['minimum' => $min, 'optimum' => $opt]);

        echo $this->form_end($form);

    }) ?>

  </div>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){

    $('#autoform_one_round input[type="radio"]').on('change', function() {
        var $help = $(this).closest('.input-wrap').find('.help-text');
        $active = $help.find('span').eq(1-$(this).val()).removeClass('hidden');
        $help.find('span').not($active).addClass('hidden');
    });

    $('.autoform').on('change', '.cost-item .type select', function() {
        $(this).closest('.type').find('img').attr('src', '<?= $this->ee($this->asset('img/project/needs/'), 'js') ?>' + $(this).val() + '.png');
    });

    // Send the form via AJAX
    $('.autoform').on('click', '.add-cost', function(e){
        e.preventDefault();
        var $form = $(this).closest('form');
        var $list = $form.find('.cost-list');
        var serial = $form.serialize() + '&' + encodeURIComponent($(this).attr('name')) + '=';
        console.log('add cost', serial);

        $but = $(this).hide();
        $list.find('>.text-danger').remove();
        $list.append('<div class="loading"></div>');
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: serial
        }).done(function (data) {
            var $data = $(data);
            $list.append($data.hide());
            $data.slideDown();
        }).fail(function (data) {
            $list.append('<p class="text-danger">' + data + '</p>');
        }).always(function() {
            $but.show();
            $list.find('>.loading').remove();
        });
    });

    $('.autoform').on('click', '.remove-cost', function(e){
        e.preventDefault();
        var $form = $(this).closest('form');
        var $list = $form.find('.cost-list');
        var serial = $form.serialize() + '&' + encodeURIComponent($(this).attr('name')) + '=';
        var $item = $(this).closest('.panel');
        $(this).replaceWith('<div class="loading"></div>');
        $item.find(':input').attr('disabled', true);
        console.log('del cost', serial);
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: serial
        }).done(function () {
            $item.slideUp(function(){
                $(this).remove();
            });
        }).fail(function (data) {
            console.log('An error occurred.', data);
            alert(data.responseText);
        });
    });

    var setBar = function() {
        var $panel = $(this).closest('.panel-body');
        var $container = $(this).closest('.inner-container');
        var $bar = $container.find('.progress');

        var required = parseInt($panel.find('.required select').val(), 10);
        if(required) {
            $panel.removeClass('lilac');
        } else {
            $panel.addClass('lilac');
        }
        var min = opt = 0;
        $container.find('.amount input').each(function() {
            var amount = parseInt($(this).closest('.panel-body').find('.amount input').val(), 10);
            if(parseInt($(this).closest('.panel-body').find('.required select').val(), 10)) {
                min += amount;
            } else {
                opt += amount;
            }

        });
        $bar.find('.minimum > span').html(min);
        $bar.find('.optimum > span').html(opt);
        $bar.find('.minimum').css('width', Math.round(100*min/(min+opt)) + '%');
        $bar.find('.optimum').css('width', Math.round(100*opt/(min+opt)) + '%');
    };

    $('.autoform').on('change', '.cost-item .required select', setBar);
    $('.autoform').on('change', '.cost-item .amount input', setBar);
});

// @license-end
</script>
<?php $this->append() ?>