<?php

    // Include our configuration settings
    require_once 'config.php';

    // Include our OAuth functions
    require_once 'functions.php';

    $client = new Evernote\Client(array('token' => $token));
    $userStore = $client->getUserStore();
    $userStore->getUser();

    $noteStore = $client->getNoteStore();

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    	$action = $_POST['action'];
    	// If a save is necessary
    	if ($action == 'save'){
	    	$noteContent = $_POST['noteContent'];
	    	$noteTitle = $_POST['noteTitle'];
	    	$noteGuid = $_POST['noteGuid'];
    		try{
    			saveNote($noteStore, $noteTitle, $noteGuid, $noteContent);
    			echo json_encode(array('success'=> True));
    		} catch (EDAMUserException $edue) {
		        // Something was wrong with the note data
		        // See EDAMErrorCode enumeration for error code explanation
		        // http://dev.evernote.com/documentation/reference/Errors.html#Enum_EDAMErrorCode
		        echo json_encode(array('success'=>False, 'error'=>'EDAMUserException'));
		    } catch (EDAMNotFoundException $ednfe) {
		        // Parent Notebook GUID doesn't correspond to an actual notebook
		        echo json_encode(array('success'=>False, 'error'=>'EDAMNotFoundException'));
		    }
		// If need to create notebook
    	} else if ($action == 'create_notebook'){
    		$notebookTitle = $_POST['notebookTitle'];
    		try{
    			$newNotebook = makeNotebook($noteStore, $notebookTitle);
    			$toReturn = array('success'=> True, 'notebook_guid' => $newNoteBook->guid, 'note_name'=>$newNoteBook->name);
    			echo json_encode($toReturn);
    		} catch (EDAMUserException $edue) {
		        // Something was wrong with the note data
		        // See EDAMErrorCode enumeration for error code explanation
		        // http://dev.evernote.com/documentation/reference/Errors.html#Enum_EDAMErrorCode
		        echo json_encode(array('success'=>False, 'error'=>'EDAMUserException'));
		    } catch (EDAMNotFoundException $ednfe) {
		        // Parent Notebook GUID doesn't correspond to an actual notebook
		        echo json_encode(array('success'=>False, 'error'=>'EDAMNotFoundException'));
		    }
		// If need to create note
    	} else if ($action == 'create_note'){
	    	$noteContent = $_POST['noteContent'];
	    	$noteTitle = $_POST['noteTitle'];
	    	if(isset($_POST['notebook_guid'])){
	    		$parentNotebook = $noteStore->getNotebook($_POST['notebook_guid']);
	    	} else {
	    		$parentNotebook = null;
	    	}
	    	try{
	    		$newNote = makeNote($noteStore, $noteTitle, $noteContent, $parentNotebook);
	    		$toReturn = array('success'=>True, 'note_guid'=>$newNote->guid, 'note_name'->$newNote->name);
	    		echo json_encode($toReturn);
	    	} catch (EDAMUserException $edue) {
		        // Something was wrong with the note data
		        // See EDAMErrorCode enumeration for error code explanation
		        // http://dev.evernote.com/documentation/reference/Errors.html#Enum_EDAMErrorCode
		        echo json_encode(array('success'=>False, 'error'=>'EDAMUserException'));
		    } catch (EDAMNotFoundException $ednfe) {
		        // Parent Notebook GUID doesn't correspond to an actual notebook
		        echo json_encode(array('success'=>False, 'error'=>'EDAMNotFoundException'));
		    }
    	}
    }
?>