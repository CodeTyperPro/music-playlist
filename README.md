# Music Playlist Web Application

The Music Playlist Web Application is a PHP-based server-side web application that allows users to browse and create playlists of their favorite music tracks. The application provides an intuitive and user-friendly interface for seamless music discovery and playlist management.

## Demo Prototype

![Demo](demo.gif)

## Home Page

Upon landing on the home page, users are greeted with an enticing title and a captivating description of the application's features. The home page serves as a central hub where both authenticated and unauthenticated users can explore and enjoy the public playlists shared by the community. Each playlist is presented with essential information, including the playlist name, the number of tracks it contains, the username of the creator, and a button to view the playlist details. Furthermore, an integrated search field empowers users to quickly find specific tracks by their titles.

## Playlist Details Page

The playlist details page offers a comprehensive view of a selected playlist. Users can delve into the playlist's content, discovering vital information about each track, such as the track title, artist name, track length, year of release, and associated genres. Authenticated users have the privilege to add their preferred tracks to their own playlists directly from this page, utilizing an intuitive drop-down menu.

## User Authentication

To unlock personalized features and maximize their experience, users can register and log in to the application. The registration process mandates the provision of a username, email address, and password. Data validation ensures that all required fields are filled accurately, and appropriate error messages are displayed if any discrepancies arise. User-friendly forms retain previously entered data to facilitate a smooth registration experience. Upon successful registration, user data is securely stored, and users are seamlessly redirected to the login page. Existing users can effortlessly access the application by providing their username and password. In case of login errors, informative messages are displayed to guide users. Successful login leads users back to the home page, ready to explore the vast collection of playlists.

## Personal Playlists

The Music Playlist Web Application offers a personalized touch by granting authenticated users the ability to create and manage their own playlists. Users can effortlessly create new playlists, specifying whether they should be public or private. They also have the convenience of adding tracks from public playlists or utilizing the search functionality to discover new tracks and enrich their personal playlists. Furthermore, users can remove tracks from their playlists, providing full control over their curated music collections.

## Admin Features

To ensure seamless administration of the application, a dedicated user with administrative privileges, named "admin," is provided. The admin user has exclusive capabilities, including the ability to add new tracks to the application's data, edit existing track information, and safely delete tracks while considering the potential impact on playlists.

## Implementation and Design

The Music Playlist Web Application offers a sophisticated and visually appealing user interface. Design options range from a minimalist approach to a customized look, incorporating unique style sheets and graphical elements. While there are no strict expectations regarding the visual appearance, usability remains a paramount concern. To ensure compatibility, the `novalidate` attribute is added to `<form>` elements, disabling browser-level validation. Server-side validation is implemented to guarantee data integrity.

During the development process, it is recommended to create a static prototype using HTML and CSS. This serves as a valuable tool for planning the layout and conditional elements. Additionally, careful consideration should be given to data storage and retrieval. While various options are available, utilizing .json files and the Storage class is highly recommended. Providing example data files along with the submission is crucial for evaluation purposes. Alternatively, a database solution can be employed, such as SQLite with PDO, ensuring proper database structure and example data are included.

## Conclusion

The Music Playlist Web Application offers an immersive and user-centric platform for discovering, creating, and managing music playlists. By combining robust backend functionality with an appealing user interface, the application aims to deliver a seamless and enjoyable music browsing experience.