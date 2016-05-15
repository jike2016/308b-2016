<?php
/**
 * 添加水印
 * ͼƬ��ˮӡ��������png/jpg/gif��ʽ��
 * 
 * @author flynetcn
 *
 * @param $srcImg ԭͼƬ
 * @param $waterImg ˮӡͼƬ
 * @param $savepath ����·��
 * @param $savename ��������
 * @param $positon ˮӡλ�� 
 * 1:��������, 2:��������, 3:����, 4:�ײ�����, 5:�ײ����� 
 * @param $alpha ͸���� -- 0:��ȫ͸��, 100:��ȫ��͸��
 * 
 * @return �ɹ� -- ��ˮӡ�����ͼƬ��ַ
 *          ʧ�� -- -1:ԭ�ļ�������, -2:ˮӡͼƬ������, -3:ԭ�ļ�ͼ�������ʧ��
 *          -4:ˮӡ�ļ�ͼ�������ʧ�� -5:��ˮӡ�����ͼƬ����ʧ��
 */
function img_water_mark($sourcepic,$watermark,$position){
$stamp = imagecreatefrompng($watermark);
$picstr=strrchr($sourcepic,'.');
if($picstr==='.png'){
$im = imagecreatefrompng($sourcepic);
imagecopy($im, $stamp, 0, 0, 0, 0, imagesx($stamp), imagesy($stamp));
imagepng($im, $sourcepic); 
}
else if($picstr==='.gif'){
	$im = imagecreatefromgif($sourcepic);
imagecopy($im, $stamp, 0, 0, 0, 0, imagesx($stamp), imagesy($stamp));
imagepng($im, $sourcepic); 
}
else if($picstr==='.jpeg'||$picstr==='.jpg'){
$im= imagecreatefromjpeg($sourcepic);
imagecopy($im, $stamp, 0, 0, 0, 0, imagesx($stamp), imagesy($stamp));
imagejpeg($im, $sourcepic); 
}
imagedestroy($im); 

}

