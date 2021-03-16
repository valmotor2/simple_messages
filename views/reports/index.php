<?php 

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

?>

	<div class="row">
        <div class="col-md-8">
            <h5>Status Servicii</h5>
        </div>
        <div class="col-md-4">
            <?=Html::a('<span class="label label-info">Reimprospatare</span>', Url::toRoute(['/reports/index']));?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if(strpos($stats->getStatus(), 'running') === false): ?>
                <div class="alert alert-danger">
                    <?=nl2br($stats->getStatus());?>
                </div>
            <?php else:  ?>
                <div class="alert alert-success">
                    <?=nl2br($stats->getStatus());?>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="well">
                            <h5>Sms</h5>
                            <?php $st = $stats->getStatusSms(); ?>
                            <p>Mesaje primite: <strong><?=$st->received->total;?></strong><br />
                                Mesaje trimise: <strong><?=$st->sent->total;?></strong><br />
                                Mesaje in asteptare pentru trimitere: <strong><?=$st->sent->queued;?></strong><br />
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="well">
                            <h5>Confirmari status mesaje</h5>
                            <?php $st = $stats->getDlr(); ?>
                            <p>In asteptare: <strong><?=$st->queued;?></strong><br />
                                Confirmate receptionate: <strong><?=$st->received->total;?></strong><br />
                                Confirmate trimise: <strong><?=$st->sent->total;?></strong><br />
                                Tip Stocare: <strong><?=$st->storage?></strong>
                            </p>
                        </div>
                    </div>
                </div>


            <?php endif; ?>
        </div>
    </div>
    <div class="row">
		
        <div class="col-md-12">
		
            <?php if(empty($stats->getStatus())): ?>
                <div class="row-fluid">
                    <div class="row-fluid">
                        <div class="alert alert-warning">
                            <h3>Modulul de trimitere SMS este dezactivat </h3>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                </div>
                <div class="span6">
                    <h5>Echipamente <?=count($stats->getSmscs());?> (total)</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Echipament</th>
                            <th>Queued</th>
                            <th>Failed</th>
                            <th>SMS (Sent|Rcv)</th>
                            <th>DLR (Sent|Rcv)</th>
                            <th>Sts</th>
                        </tr>
                        <?php foreach($stats->getSmscs() as $key => $each): ?>
                        <tr>
                            <td><?=Html::encode($each['smsc_id']); ?></td>
                            <td><?=Html::encode($each['smsc_queued']); ?></td>
                            <td><?=Html::encode($each['smsc_failed']); ?></td>
                            <td> 
                                <?=Html::encode($each['sms']['sent']);?> |
                                <?=Html::encode($each['sms']['received']);?>
                            </td>
                            <td>
                                <?=Html::encode($each['dlr']['sent']);?> |
                                <?=Html::encode($each['dlr']['received']);?>
                            </td>
                            <td><?=Html::encode($each['smsc_status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <div class="span3">
                    <h5>Versiune</h5>
                    <?=nl2br($stats->getVersion());?>
                </div>
            </div>
            <?php endif;?>
        </div>
    </div>	
