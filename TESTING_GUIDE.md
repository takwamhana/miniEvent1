# 🧪 Admin Dashboard - Testing Guide

**Version**: 2.0  
**Date**: March 29, 2026  
**Status**: Ready for Testing

---

## 🎯 Quick Start

### 1. Start Docker Containers

```bash
cd C:\Users\mhana\miniEvent1
docker-compose up -d
```

### 2. Verify Containers Are Running

```bash
docker-compose ps
```

You should see:

- ✅ php (running)
- ✅ nginx (running)
- ✅ postgres (running)

### 3. Run Migrations (if needed)

```bash
docker-compose exec php bin/console doctrine:migrations:migrate
```

### 4. Create Admin User (if needed)

```bash
docker-compose exec php bin/console doctrine:query:sql "INSERT INTO \"user\" (email, roles, password) VALUES ('admin@test.com', '[\"ROLE_ADMIN\"]', NULL);"
```

### 5. Access the Application

- **Homepage**: http://localhost:8080
- **Login**: http://localhost:8080/login.html
- **Admin Dashboard**: http://localhost:8080/admin.html

---

## 🔐 Authentication Testing

### Test 1: Login with Passkey

**Steps**:

1. Go to http://localhost:8080/login.html
2. Click "Inscription" tab
3. Enter email: `admin@test.com`
4. Click "Créer un compte avec Passkey"
5. Follow browser prompts

**Expected Result**:

- ✅ Passkey created successfully
- ✅ Redirected to index.html
- ✅ Token stored in localStorage
- ✅ User data stored in localStorage

**How to Verify**:

- Open browser DevTools (F12)
- Go to Console tab
- Type: `localStorage.getItem('token')`
- Should return a JWT token
- Type: `localStorage.getItem('user')`
- Should return user JSON with ROLE_ADMIN

---

### Test 2: Access Admin Dashboard

**Steps**:

1. After login, go to http://localhost:8080/admin.html
2. Wait for page to load

**Expected Result**:

- ✅ "Vérification de l'authentification..." disappears
- ✅ Dashboard content appears
- ✅ User email displayed at top
- ✅ No console errors

**What You Should See**:

```
Header:
- "Mini Event - Administration"
- "Connecté en tant que admin@test.com"
- "Voir le site" button
- "Déconnexion" button

Content:
- "Créer un nouvel événement" form
- "📋 Liste des Événements" section
- "👥 Gestion des Participants" section
```

**Browser Console Should Show**:

```
Checking authentication...
Token: eyJ0eXAiOiJKV1QiLCJhbGci...
User: {"id":1,"email":"admin@test.com","roles":["ROLE_ADMIN"]}
Current user: {id: 1, email: 'admin@test.com', roles: Array(1)}
Authentication successful
Loading events...
Events loaded: [...]
```

---

### Test 3: Unauthorized Access

**Steps**:

1. Open new Incognito/Private window
2. Go to http://localhost:8080/admin.html directly

**Expected Result**:

- ✅ Shows "Non authentifié. Redirection en cours..."
- ✅ Redirects to login.html after 2 seconds

---

## 📅 Events Management Testing

### Test 4: Create Event

**Steps**:

1. In admin dashboard, scroll to "Créer un nouvel événement"
2. Fill form:
    - **Titre**: "Test Event"
    - **Lieu**: "Paris, France"
    - **Date et heure**: (Select future date/time)
    - **Nombre de places**: 100
    - **Description**: "This is a test event"
    - **URL de l'image**: (leave empty or add URL)
3. Click "Créer l'événement"

**Expected Result**:

- ✅ Button shows "Création en cours..." while processing
- ✅ Green success message: "Événement créé avec succès !"
- ✅ Form clears
- ✅ New event appears in "Liste des Événements" table
- ✅ New event appears in "Gestion des Participants" dropdown

**Browser Console Should Show**:

```
Creating event...
Event created successfully
Loading events...
Events loaded: [...]
```

---

### Test 5: Edit Event

**Steps**:

1. In "Liste des Événements" table, click "Modifier" on an event
2. Verify form populates with event data
3. Change the title to "Updated Event"
4. Change seats to 150
5. Click "Mettre à jour l'événement"

**Expected Result**:

- ✅ Form scrolls into view
- ✅ All fields populate correctly
- ✅ Button text changes to "Mettre à jour l'événement"
- ✅ After clicking, button shows "Mise à jour en cours..."
- ✅ Green success message: "Événement mis à jour avec succès !"
- ✅ Form clears
- ✅ Button text changes back to "Créer l'événement"
- ✅ Events table refreshes with new data

**Browser Console Should Show**:

```
Updating event...
Event updated successfully
Loading events...
Events loaded: [...]
```

---

### Test 6: Delete Event

**Steps**:

1. In "Liste des Événements" table, click "Supprimer" on an event
2. Confirm deletion in popup

**Expected Result**:

- ✅ Confirmation dialog appears
- ✅ After confirming, green success message: "Événement supprimé avec succès !"
- ✅ Event disappears from table
- ✅ Events table refreshes

**Browser Console Should Show**:

```
Deleting event...
Event deleted successfully
Loading events...
Events loaded: [...]
```

---

## 👥 Reservations Management Testing

### Test 7: Create Reservation (Frontend)

**Steps**:

1. Open http://localhost:8080 in new tab
2. Click on an event
3. Fill reservation form:
    - **Nom**: "John Doe"
    - **Email**: "john@example.com"
    - **Téléphone**: "+33612345678"
4. Click "Réserver"

**Expected Result**:

- ✅ Success message appears
- ✅ Confirmation email sent (check mailpit at http://localhost:8025)

---

### Test 8: View Reservations

**Steps**:

1. Go back to admin dashboard
2. In "Gestion des Participants" section
3. Select an event from dropdown

**Expected Result**:

- ✅ Reservations appear in both sections:
    - Reservation cards with details
    - Participants table with all columns
- ✅ Shows count: "Liste des Participants (X)"
- ✅ Each reservation shows: Name, Email, Phone, Date
- ✅ Edit (✏️) and Delete (🗑️) buttons appear

**If No Reservations**:

- ✅ Shows "Aucune réservation pour cet événement."
- ✅ Shows "Aucun participant pour cet événement."

---

### Test 9: Edit Reservation

**Steps**:

1. Select an event with reservations
2. Click "✏️" button on a reservation
3. Change name to "Jane Doe"
4. Click OK
5. Change email to "jane@example.com"
6. Click OK
7. Change phone to "+33698765432"
8. Click OK

**Expected Result**:

- ✅ Each prompt shows current value
- ✅ After all prompts, success message: "Réservation modifiée avec succès !"
- ✅ Reservations list refreshes
- ✅ Shows updated data

**Browser Console Should Show**:

```
Editing reservation...
Reservation updated successfully
Loading reservations...
Reservations loaded: [...]
```

---

### Test 10: Delete Reservation

**Steps**:

1. Select an event with reservations
2. Click "🗑️" button on a reservation
3. Confirm deletion

**Expected Result**:

- ✅ Confirmation dialog appears
- ✅ After confirming, success message: "Réservation supprimée avec succès !"
- ✅ Reservation disappears from both sections
- ✅ Count updates

**Browser Console Should Show**:

```
Deleting reservation...
Reservation deleted successfully
Loading reservations...
Reservations loaded: [...]
```

---

## 🔒 Security Testing

### Test 11: XSS Protection

**Steps**:

1. Create reservation with malicious name (from frontend or API):
    ```
    Name: <script>alert('XSS')</script>
    Email: test@test.com
    Phone: 123456789
    ```
2. Go to admin dashboard
3. Select the event
4. View reservations

**Expected Result**:

- ✅ Name displays as literal text: `<script>alert('XSS')</script>`
- ✅ No JavaScript alert appears
- ✅ No script execution in browser
- ✅ No console errors

**How to Verify**:

- Right-click the name in browser
- Select "Inspect Element"
- Verify the HTML is escaped:
    ```html
    &lt;script&gt;alert('XSS')&lt;/script&gt;
    ```

---

### Test 12: Authentication Required

**Steps**:

1. Open DevTools (F12)
2. Go to Console
3. Type: `localStorage.clear()`
4. Refresh page

**Expected Result**:

- ✅ Dashboard hides
- ✅ Shows "Non authentifié" message
- ✅ Redirects to login.html

---

### Test 13: Admin Role Required

**Steps**:

1. Manually edit user in localStorage to remove ROLE_ADMIN:
    ```javascript
    let user = JSON.parse(localStorage.getItem("user"));
    user.roles = ["ROLE_USER"];
    localStorage.setItem("user", JSON.stringify(user));
    ```
2. Refresh page

**Expected Result**:

- ✅ Shows "Accès non autorisé. Vous devez être administrateur."
- ✅ Redirects to index.html

---

## ❌ Error Handling Testing

### Test 14: Create Event Errors

**Steps**:

1. Try to create event with invalid data
2. Or disconnect internet temporarily
3. Submit form

**Expected Result**:

- ✅ Red error message appears
- ✅ Error message shows specific error from API
- ✅ Form doesn't clear
- ✅ Button re-enables

---

### Test 15: Network Errors

**Steps**:

1. Open DevTools
2. Go to Network tab
3. Select "Offline" in throttling dropdown
4. Try any operation (create, edit, delete)

**Expected Result**:

- ✅ Red error message appears
- ✅ Shows: "Failed to fetch" or similar
- ✅ Button re-enables
- ✅ No crash

---

## 🎨 UI/UX Testing

### Test 16: Loading States

**What to Check**:

- ✅ Button text changes during operations
- ✅ Buttons disable during operations
- ✅ Loading messages appear when appropriate
- ✅ Success messages auto-hide after 5 seconds
- ✅ Error messages auto-hide after 5 seconds

---

### Test 17: Responsive Design

**Steps**:

1. Resize browser window
2. Test on mobile device (DevTools device emulation)

**Expected Result**:

- ✅ Layout adapts to screen size
- ✅ Forms remain usable
- ✅ Tables scroll horizontally if needed
- ✅ Buttons remain accessible

---

### Test 18: Empty States

**What to Check**:

- ✅ "Aucun événement trouvé." when no events
- ✅ "Sélectionnez un événement..." in dropdown
- ✅ "Aucune réservation..." when event has no reservations
- ✅ Proper placeholder messages everywhere

---

## 🐛 Common Issues & Solutions

### Issue 1: Dashboard shows "Vérification de l'authentification..." forever

**Solution**:

- Check browser console for errors
- Verify you're logged in: `localStorage.getItem('token')`
- Clear localStorage and re-login
- Check if API is responding: `fetch('/api/events').then(r => r.json()).then(console.log)`

---

### Issue 2: "Non authentifié" error

**Solution**:

- You need to login first at http://localhost:8080/login.html
- Verify token exists: `localStorage.getItem('token')`
- Token may be expired - logout and login again

---

### Issue 3: "Accès non autorisé" error

**Solution**:

- User doesn't have ROLE_ADMIN
- Check user roles: `JSON.parse(localStorage.getItem('user')).roles`
- Create new admin user via SQL:
    ```sql
    INSERT INTO "user" (email, roles, password)
    VALUES ('admin@test.com', '["ROLE_ADMIN"]', NULL);
    ```

---

### Issue 4: Events don't load

**Solution**:

- Check API response in Network tab (DevTools)
- Verify backend is running: `docker-compose ps`
- Check backend logs: `docker-compose logs php`
- Test API directly: http://localhost:8080/api/events

---

### Issue 5: Form doesn't submit

**Solution**:

- Check browser console for JavaScript errors
- Verify all required fields are filled
- Check Network tab for failed requests
- Verify token is valid

---

### Issue 6: Reservations don't load

**Solution**:

- Verify event is selected in dropdown
- Check API endpoint: `/api/admin/events/{id}/reservations`
- Verify user has ROLE_ADMIN
- Check backend logs for errors

---

## ✅ Success Criteria

### All tests pass if:

**Authentication**:

- [x] Can login with Passkey
- [x] Dashboard loads for admin users
- [x] Redirects non-authenticated users
- [x] Redirects non-admin users

**Events Management**:

- [x] Can create events
- [x] Can edit events
- [x] Can delete events
- [x] Events list updates in real-time

**Reservations Management**:

- [x] Can view reservations
- [x] Can edit reservations
- [x] Can delete reservations
- [x] Both display sections work

**Security**:

- [x] XSS attacks prevented
- [x] User input escaped
- [x] Authentication enforced
- [x] Admin role enforced

**Error Handling**:

- [x] Network errors handled gracefully
- [x] API errors show user-friendly messages
- [x] No console errors during normal usage
- [x] Forms recover from errors

**UI/UX**:

- [x] Loading states visible
- [x] Success messages appear
- [x] Error messages appear
- [x] Responsive design works

---

## 📊 Performance Checklist

- [ ] Page loads in < 2 seconds
- [ ] Events list loads in < 1 second
- [ ] Reservations load in < 1 second
- [ ] Form submissions respond in < 2 seconds
- [ ] No unnecessary API calls
- [ ] No memory leaks (check DevTools Memory tab)

---

## 🎉 Final Verification

### Quick Test Sequence (5 minutes):

1. ✅ Login as admin
2. ✅ Access dashboard
3. ✅ Create 1 event
4. ✅ Edit the event
5. ✅ Create reservation (frontend)
6. ✅ View reservations (admin)
7. ✅ Edit reservation
8. ✅ Delete reservation
9. ✅ Delete event
10. ✅ Logout

**If all 10 steps work**: 🎉 **Dashboard is READY!**

---

## 📞 Support

### Need Help?

1. **Check console**: Open DevTools (F12) → Console tab
2. **Check network**: DevTools → Network tab → Look for failed requests
3. **Check backend**: `docker-compose logs php`
4. **Check database**: Connect to PostgreSQL and verify data
5. **Clear cache**: Ctrl+Shift+R (hard refresh)
6. **Reset state**: `localStorage.clear()` and re-login

### Debugging Commands:

```javascript
// Check authentication
console.log("Token:", localStorage.getItem("token"));
console.log("User:", JSON.parse(localStorage.getItem("user")));

// Check events
fetch("/api/events")
    .then((r) => r.json())
    .then(console.log);

// Check specific event reservations (replace 1 with event ID)
fetch("/api/admin/events/1/reservations", {
    headers: {
        Authorization: "Bearer " + localStorage.getItem("token"),
    },
})
    .then((r) => r.json())
    .then(console.log);
```

---

**Happy Testing! 🚀**

---

**Generated**: March 29, 2026  
**Version**: 1.0  
**Status**: Ready for Use
