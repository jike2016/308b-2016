/**
 * @author xdw
 * 表格字段点击排序控制
 */

/**
 * 主函数
 * @param sTableID 表id
 * @param iCol 列索引（第几列）
 * @param sDataType 该列的数据类型
 */
function  sortTable(sTableID, iCol, sDataType) {
    var  oTable = document.getElementById(sTableID);
    var  oTBody = oTable.tBodies[0];
    var  colDataRows = oTBody.rows;
    var  aTRs =  new  Array;

    for  (  var  i = 0; i < colDataRows.length; i++) {
        aTRs[i] = colDataRows[i];
    }
    if  (oTable.sortCol == iCol) {
        aTRs.reverse();
    }  else  {
        aTRs.sort(generateCompareTRs(iCol, sDataType));
    }
    var  oFragment = document.createDocumentFragment();
    for  (  var  j = 0; j < aTRs.length; j++) {
        aTRs[j].cells[0].innerHTML = j+1;//修改因排序后打乱的序号
        oFragment.appendChild(aTRs[j]);
    }

    oTBody.appendChild(oFragment);
    oTable.sortCol = iCol;
}

//数据两两比较
function  generateCompareTRs(iCol, sDataType) {
    return   function  compareTRs(oTR1, oTR2) {
        vValue1 = convert(oTR1.cells[iCol].firstChild.nodeValue, sDataType);
        vValue2 = convert(oTR2.cells[iCol].firstChild.nodeValue, sDataType);
        if  (vValue1 < vValue2) {
            return  -1;
        }  else   if  (vValue1 > vValue2) {
            return  1;
        }  else  {
            return  0;
        }
    };
}

//类型转换
function  convert(sValue, sDataType) {
    switch  (sDataType) {
        case   "int" :
            return  parseInt(sValue);
        case   "float" :
            return  parseFloat(sValue);
        case   "date" :
            return   new  Date(Date.parse(sValue));
        case   "my_string" :
            if(sValue.length>1){
                return   parseInt(sValue.substr(0,sValue.length-1));
            }else{
                return   -1;
            }
        default :
            return  sValue.toString();
    }
}
