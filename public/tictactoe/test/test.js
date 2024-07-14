var slots = [8];
compTurn = 0;

function checkWin(){
    //check rows
    var i;
    for(i = 0; i<9; i+=3){
        if(slots[i]==slots[i+1] && slots[i] == slots[i+2]){
            compTurn = 0;
            return [i, i+1, i+2];
        }
    }
    
    //check the colomns 
    for(i=0; i<3; i++){
        if(slots[i]==slots[i+3] && slots[i]==slots[i+6]){
            compTurn = 0;
            return [i, i+3, i+6];
        }
    }

    //check diagonals
    if(slots[0]==slots[4] && slots[0]==slots[8]){
        compTurn = 0;
        return [0, 4, 8];
    }

    if(slots[0]==slots[2] && slots[0]==slots[6]){
        compTurn = 0;
        return [2, 4, 6];
    }
}

function compMove(){
    switch (compTurn){
        case 0:
            if (slots[4]==""){
                slots[4]="O";
                return 4;
            }
            else{
                slots[0]="O"
                return = 0;
            }

        case 1:
            
    }
}

}